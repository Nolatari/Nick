<?php

namespace Nick;

use Exception;

/**
 * Class Core
 *
 * @package Nick
 */
class Core {

  /**
   * Creates content items on bootstrapping Nick
   */
  public function createMatters() {
    // Create tables for Matters.
    $matters = [];
    foreach (self::getAllMatterClasses() as $matter) {
      if (!self::matterInstalled($matter)) {
        $matters[] = self::getMatterClassFromType($matter);
      }
    }

    foreach ($matters as $matter) {
      $matter::create();
    }
  }

  /**
   * Sets exception handler.
   */
  public function setSystemSpecifics() {
    @set_exception_handler([$this, 'Exception']);
  }

  /**
   * Logs exceptions through Nick's Logger class.
   *
   * @param Exception $exception
   */
  public function Exception($exception) {
    \Nick::Logger()->add($exception->getMessage(), Logger::TYPE_ERROR, 'Exception');
  }

  /**
   * @param $type
   *
   * @return mixed
   */
  public static function getMatterClassFromType($type) {
    self::loadMatterClassFile($type);

    $className = '\\Nick\\' . $type . '\\' . $type;
    return new $className;
  }

  /**
   * @param $type
   *
   * @return bool
   */
  public static function loadMatterClassFile($type) {
    $dirs = self::getCoreExtensions();

    foreach ($dirs as $dir) {
      if (strtolower($dir) === $type) {
        if (is_file(__DIR__ . '/' . $dir . '/' . $dir . 'Interface.php')) {
          require_once __DIR__ . '/' . $dir . '/' . $dir . 'Interface.php';
        }
        if (is_file(__DIR__ . '/' . $dir . '/' . $dir . '.php')) {
          require_once __DIR__ . '/' . $dir . '/' . $dir . '.php';
        }
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * @return array
   */
  protected static function getCoreExtensions() {
    return array_map(function ($item) {
      return str_replace('core/components/', '', $item);
    }, array_filter(glob('core/components/*'), 'is_dir'));
  }

  /**
   * @return array
   */
  protected static function getAllMatterClasses() {
    $matters = [];
    $extensionsChecked = [];
    foreach (self::getCoreExtensions() as $extension) {
      $extensionsChecked[] = $extension;
      $extensionInfo = YamlReader::readCoreExtension($extension);
      if ($extensionInfo['type'] !== 'matter') {
        continue;
      }

      $matters[] = $extension;
    }
    return $matters;
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
    $database = \Nick::Database();
    $type = strtolower($type);
    $matter = $database->select('matter__' . $type);
    if (!$matter->execute()) {
      return FALSE;
    }
    $matter_storage = $database->select('matter_storage')
      ->condition('type', $type);
    if (!$matter_storage->execute()) {
      return FALSE;
    }
    if ($result = $matter_storage->fetchAllAssoc()) {
      if (count($result) > 0) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
