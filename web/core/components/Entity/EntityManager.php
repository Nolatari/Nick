<?php

namespace Nick\Entity;

use Exception;
use Nick;
use Nick\Database\Result;
use Nick\Logger;
use Nick\YamlReader;

/**
 * Class EntityManager
 *
 * @package Nick\Entity
 */
class EntityManager {

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
    \Nick::Logger()->add('Removed ' . ucfirst($type) . ' entity type and entities', Logger::TYPE_INFO, ucfirst($type));

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
      if (!$object instanceof EntityInterface) {
        continue;
      }

      if (!method_exists($object, 'create')) {
        continue;
      }

      $object::create();
    }
  }

  /**
   * Updates entity fields with initial ones.
   */
  public function updateEntities() {
    $entities = self::getAllEntities();
    foreach ($entities as $entity => $info) {
      if (!self::entityInstalled($entity)) {
        continue;
      }

      $object = new $info['class'];
      if (!$object instanceof EntityInterface) {
        continue;
      }

      \Nick::Database()->update('entity_storage')
        ->condition('type', $object->getType())
        ->values([
          'fields' => serialize($object::initialFields()),
        ])
        ->execute();
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
      if ($properties === 'type') {
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