<?php

namespace Nick\Matter;

use Exception;
use Nick;
use Nick\Logger;
use Nick\YamlReader;
use Nick\Database\Result;

/**
 * Class MatterManager
 *
 * @package Nick\Matter
 */
class MatterManager {

  /**
   * @return array
   */
  protected static function getAllMatterClasses() {
    $matters = [];
    $extensions = Nick::ExtensionManager()::getCoreExtensions() + Nick::ExtensionManager()::getContribExtensions();
    foreach ($extensions as $extension) {
      $extensionInfo = YamlReader::readExtension($extension);
      if ($extensionInfo['type'] !== 'matter') {
        continue;
      }

      $matters[] = $extension;
    }
    return $matters;
  }

  /**
   * @param $type
   *
   * @return mixed
   */
  public static function getMatterClassFromType($type) {
    self::loadMatterClassFile($type);

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
  public static function loadMatterClassFile($type) {
    $dirs = Nick::ExtensionManager()::getCoreExtensions() + Nick::ExtensionManager()::getContribExtensions();

    foreach ($dirs as $dir) {
      if (strtolower($dir) === $type) {
        // Include interface first
        if (is_file(__DIR__ . '/' . $dir . '/' . $dir . 'Interface.php')) {
          require_once __DIR__ . '/' . $dir . '/' . $dir . 'Interface.php';
        }
        // Include the matter's class file
        if (is_file(__DIR__ . '/' . $dir . '/' . $dir . '.php')) {
          require_once __DIR__ . '/' . $dir . '/' . $dir . '.php';
        }
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Checks whether matter is installed
   *
   * @param string $type
   *            Machine readable label of matter.
   *
   * @return bool
   */
  public static function matterInstalled($type) {
    $database = Nick::Database();
    $type = strtolower($type);
    $matter = $database
      ->select('matter__' . $type)
      ->execute();
    if (!$matter instanceof Result) {
      return FALSE;
    }
    $matter_storage = $database
      ->select('matter_storage')
      ->condition('type', $type)
      ->execute();
    if (!$matter_storage instanceof Result) {
      return FALSE;
    }
    if ($result = $matter_storage->fetchAllAssoc()) {
      if (count($result) > 0) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Creates content items on bootstrapping Nick
   */
  public function createMatters() {
    // Create tables for Matters.
    $matters = [];
    foreach (self::getAllMatterClasses() as $matter) {
      if (!self::matterInstalled($matter) && Nick::ExtensionManager()::extensionInstalled($matter)) {
        $matters[] = self::getMatterClassFromType($matter);
      }
    }

    foreach ($matters as $matter) {
      if (!$matter instanceof MatterInterface) {
        continue;
      }

      $matter::create();
    }
  }

  /**
   * @param array $properties
   *          An array of properties your Matter should have
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
    $matter = static::getMatterClassFromType($type);
    $matter->setType($type);
    unset($properties['type']);
    $query = Nick::Database()->select('matter__' . $type)
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
      Nick::Logger()->add($exception, Logger::TYPE_FAILURE, 'Matter');
      return FALSE;
    }
    if (!$results = $result->fetchAllAssoc('id')) {
      return FALSE;
    }

    if (count($results) === 1 && $multiple === FALSE) {
      $current = reset($results);
      return $matter->massageProperties($current);
    }

    $matters = [];
    foreach ($results as $id => $current) {
      $matters[] = $matter->massageProperties($current);
    }
    return $matters;
  }

}