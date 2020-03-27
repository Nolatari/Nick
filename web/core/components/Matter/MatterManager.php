<?php

namespace Nick\Matter;

use Nick\YamlReader;
use Nick\Database\Result;
use Nick\ExtensionManager;
use Symfony\Component\DependencyInjection\Extension\Extension;

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
    $extensionsChecked = [];
    $extensions = ExtensionManager::getCoreExtensions() + ExtensionManager::getContribExtensions();
    foreach ($extensions as $extension) {
      $extensionsChecked[] = $extension;
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
    $dirs = ExtensionManager::getCoreExtensions() + ExtensionManager::getContribExtensions();

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
    $database = \Nick::Database();
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
      if (!self::matterInstalled($matter) && ExtensionManager::extensionInstalled($matter)) {
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

}