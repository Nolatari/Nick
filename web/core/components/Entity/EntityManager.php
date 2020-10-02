<?php

namespace Nick\Entity;

use Exception;
use Nick;
use Nick\Logger;
use Nick\YamlReader;
use Nick\Database\Result;

/**
 * Class EntityManager
 *
 * @package Nick\Entity
 */
class EntityManager {

  /**
   * @return array
   */
  protected static function getAllEntityClasses() {
    $entities = [];
    $extensions = Nick::ExtensionManager()::getCoreExtensions() + Nick::ExtensionManager()::getContribExtensions();
    foreach ($extensions as $extension) {
      $extensionInfo = YamlReader::readExtension($extension);
      if ($extensionInfo['type'] !== 'entity') {
        continue;
      }

      $entities[] = $extension;
    }
    return $entities;
  }

  /**
   * @param $type
   *
   * @return mixed
   */
  public static function getEntityClassFromType($type) {
    self::loadEntityClassFile($type);

    if (class_exists('\\Nick\\' . $type . '\\' . $type)) {
      $className = '\\Nick\\' . $type . '\\' . $type;
    } else {
      return FALSE;
    }
    return new $className;
  }

  /**
   * @param $type
   *
   * @return bool
   */
  public static function loadEntityClassFile($type) {
    $dirs = Nick::ExtensionManager()::getCoreExtensions() + Nick::ExtensionManager()::getContribExtensions();

    foreach ($dirs as $dir) {
      if (strtolower($dir) === $type) {
        // Include interface first
        if (is_file(__DIR__ . '/' . $dir . '/' . $dir . 'Interface.php')) {
          require_once __DIR__ . '/' . $dir . '/' . $dir . 'Interface.php';
        }
        // Include the entity's class file
        if (is_file(__DIR__ . '/' . $dir . '/' . $dir . '.php')) {
          require_once __DIR__ . '/' . $dir . '/' . $dir . '.php';
        }
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Checks whether entity is installed
   *
   * @param string $type
   *            Machine readable label of entity.
   *
   * @return bool
   */
  public static function entityInstalled($type) {
    $database = Nick::Database();
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
   * Creates content items on bootstrapping Nick
   */
  public function createEntities() {
    // Create tables for Entities.
    $entities = [];
    foreach (self::getAllEntityClasses() as $entity) {
      if (!self::entityInstalled($entity) && Nick::ExtensionManager()::extensionInstalled($entity)) {
        $entities[] = self::getEntityClassFromType($entity);
      }
    }

    foreach ($entities as $entity) {
      if (!$entity instanceof EntityInterface) {
        continue;
      }

      if (!method_exists($entity, 'create')) {
        continue;
      }

      $entity::create();
    }
  }

  /**
   * @param array $properties
   *          An array of properties your Entity should have
   * @param bool  $multiple
   *          If you expect multiple results, set this to TRUE
   *
   * @return bool|array
   *
   * @throws Exception
   */
  public function loadByProperties($properties = [], $multiple = FALSE) {
    if (!isset($properties['type'])) {
      return FALSE;
    }
    $type = $properties['type'];
    $entity = static::getEntityClassFromType($type);
    $entity->setType($type);
    unset($properties['type']);
    $query = Nick::Database()->select('entity__' . $type)
      ->condition('status', 1)
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
      Nick::Logger()->add($exception, Logger::TYPE_FAILURE, 'Entity');
      return FALSE;
    }
    if (!$results = $result->fetchAllAssoc('id')) {
      return FALSE;
    }

    if (count($results) === 1 && $multiple === FALSE) {
      $current = reset($results);
      return $entity->massageProperties($current);
    }

    $entities = [];
    foreach ($results as $id => $current) {
      $entities[] = $entity->massageProperties($current);
    }
    return $entities;
  }

}