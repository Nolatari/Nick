<?php

namespace Nick\Entity;

use Exception;
use Nick;
use Nick\Database\Result;
use Nick\Logger;
use Nick\Translation\StringTranslation;
use Nick\YamlReader;

/**
 * Class EntityManager
 *
 * @package Nick\Entity
 */
class EntityManager {
  use StringTranslation;

  /**
   * @param $type
   *
   * @return bool
   */
  public static function uninstallEntityType($type) {
    if (!self::entityInstalled($type)) {
      return FALSE;
    }
    \Nick::Database()->delete('entity')
      ->condition('type', $type)
      ->execute();
    \Nick::Database()->delete('entity_storage')
      ->condition('type', $type)
      ->execute();
    \Nick::Database()->query('DROP TABLE entity__' . $type);
    \Nick::Logger()->add('Uninstalled ' . ucfirst($type) . ' entity type and entities', Logger::TYPE_INFO, ucfirst($type));

    return TRUE;
  }

  /**
   * Creates entities on bootstrapping Nick
   */
  public function createEntities() {
    // Create tables for Entities.
    $entities = self::getAllEntities();
    foreach ($entities as $entity => $info) {
      if (self::entityInstalled($entity)) {
        unset($entities[$entity]);
      }
    }

    foreach ($entities as $entity => $info) {
      $object = new $info['class'];
      \Nick::Logger()->add($this->translate(':entity is not an instance of EntityInterface', [':entity' => $entity]), Logger::TYPE_INFO, 'EntityManager');
      if (!$object instanceof EntityInterface) {
        continue;
      }

      if (!method_exists($object, 'create')) {
        continue;
      }

      $object::create();
      \Nick::Logger()->add($this->translate('Created entity :entity', [':entity' => $entity]), Logger::TYPE_INFO, 'EntityManager');
    }
  }

  /**
   * Removes field from entity
   *
   * @param EntityInterface $entity
   * @param string          $field
   */
  public function removeEntityField(EntityInterface $entity, string $field) {
    $type = $entity->getType();
    $type_storage = \Nick::Database()->removeField('entity__' . $type, $field);
    $entity_storage_fields = \Nick::Database()
      ->select('entity_storage')
      ->condition('entity', $type)
      ->fields(NULL, ['fields']);

    if (!$type_storage) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Updates entity fields with initial ones.
   */
  public function updateEntities() {
    $entities = self::getAllEntities();
    $updated_entities = [];
    foreach ($entities as $entity => $info) {
      if (!self::entityInstalled($entity)) {
        continue;
      }

      $object = new $info['class'];
      if (!$object instanceof EntityInterface) {
        continue;
      }

      $serialized_fields = serialize($object::initialFields());
      $stored_fields_storage = \Nick::Database()->select('entity_storage')
        ->condition('type', $entity)
        ->fields(NULL, ['fields'])
        ->execute();
      if (!$stored_fields_storage instanceof Result) {
        \Nick::Logger()->add($this->translate('Could not retrieve fields storage information for :entity', [':entity' => $entity]), Logger::TYPE_ERROR, 'EntityManager');
        continue;
      }
      $results = $stored_fields_storage->fetchAllAssoc();
      $result = reset($results);
      if (isset($result['fields']) && $result['fields'] === $serialized_fields) {
        continue;
      }

      foreach ($object::initialFields() as $field => $options) {
        $field_storage = \Nick::Database()->field('entity__' . $entity, $field, $options);
        if (!$field_storage) {
          \Nick::Logger()->add($this->translate('Something went wrong trying to add/modify field [:field]', [':field' => $field]), Logger::TYPE_ERROR, 'EntityManager');
          continue;
        }
      }

      $storage_query = \Nick::Database()->update('entity_storage')
        ->condition('type', $entity)
        ->values([
          'fields' => serialize($object::initialFields()),
        ])
        ->execute();
      if (!$storage_query) {
        \Nick::Logger()->add($this->translate('Something went wrong trying to add/modify field [:field] to entity_storage', [':field' => $field]), Logger::TYPE_ERROR, 'EntityManager');
        continue;
      }

      \Nick::Logger()->add($this->translate('Updated entity storage for entity :entity', [':entity' => $entity]), Logger::TYPE_INFO, 'EntityManager');
      $updated_entities[] = $entity;
    }

    if (count($updated_entities) === 0) {
      \Nick::Logger()->add($this->translate('No entities in need of an update.'), Logger::TYPE_INFO, 'EntityManager');
    }
  }

  /**
   * @return array
   */
  protected static function getAllEntities() {
    $entities = [];
    $extensions = \Nick::ExtensionManager()::getInstalledExtensions();
    foreach ($extensions as $extension) {
      $extensionInfo = YamlReader::readExtension($extension['name']);
      if (!is_array($extensionInfo)) {
        \Nick::Logger()->add($extension['name'] . ' entry exists in database but no extension info file is found.', Logger::TYPE_FAILURE, 'EntityManager');
        continue;
      }
      if (!isset($extensionInfo['entities'])) {
        continue;
      }

      foreach ($extensionInfo['entities'] as $entity => $info) {
        $entities[$entity] = $info;
      }
    }
    return $entities;
  }

  /**
   * Checks whether entity is installed
   *
   * @param string $type
   *            Machine readable label of entity.
   *
   * @return bool
   */
  public static function entityInstalled(string $type) {
    $database = \Nick::Database();
    $type = strtolower($type);
    $entity = $database
      ->select('entity__' . $type)
      ->execute();
    if (!$entity instanceof Result) {
      return FALSE;
    }
    $entity_storage = $database
      ->select('entity_storage')
      ->condition('type', $type)
      ->execute();
    if (!$entity_storage instanceof Result) {
      return FALSE;
    }
    if ($result = $entity_storage->fetchAllAssoc()) {
      if (count($result) > 0) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * @param $type
   *
   * @return mixed
   */
  public static function getEntityClassFromType($type) {
    $entities = self::getAllEntities();
    if (!isset($entities[$type]['class'])) {
      return FALSE;
    }
    return new $entities[$type]['class'];
  }

  /**
   * @param array $properties
   *          An array of properties your Entity should have
   * @param bool  $multiple
   *          If you expect multiple results, set this to TRUE
   * @param bool  $massage
   *
   * @return bool|array
   *
   */
  public function loadByProperties($properties = [], $multiple = FALSE, $massage = TRUE) {
    if (!isset($properties['type'])) {
      return FALSE;
    }
    $type = $properties['type'];
    $entity = static::getEntityClassFromType($type);
    if (!$entity instanceof EntityInterface) {
      return FALSE;
    }
    $entity->setType($type);
    unset($properties['type']);
    $query = \Nick::Database()
      ->select('entity__' . $type)
      ->orderBy('id', 'ASC');
    foreach ($properties as $field => $value) {
      if ($field === 'type') {
        continue;
      }
      $query->condition($field, $value);
    }
    try {
      /** @var Result $result */
      $result = $query->execute();
    } catch (Exception $exception) {
      \Nick::Logger()->add($exception, Logger::TYPE_FAILURE, 'Entity');
      return FALSE;
    }
    if (!$results = $result->fetchAllAssoc('id')) {
      return FALSE;
    }

    if (count($results) === 1 && $multiple === FALSE) {
      $current = reset($results);
      return $entity->massageProperties($current, $massage);
    }

    $entities = [];
    foreach ($results as $id => $current) {
      $entities[] = $entity->massageProperties($current, $massage);
    }
    return $entities;
  }

}