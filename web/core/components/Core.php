<?php

namespace Nick;

use Exception;
use Nick\Cache\Cache;
use Nick\Cache\CacheInterface;
use Nick\Database\Result;
use Nick\Matter\MatterInterface;

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
      if (!self::matterInstalled($matter) && self::extensionInstalled($matter)) {
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

    if (class_exists('\\Nick\\' . $type . '\\' . $type)) {
      $className = '\\Nick\\' . $type . '\\' . $type;
    } elseif (class_exists('\\Nick\\Extension\\' . $type . '\\' . $type)) {
      $className = '\\Nick\\Extension\\' . $type . '\\' . $type;
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
   * @return array|bool
   */
  public static function getInstalledExtensions() {
    $extensions_storage = \Nick::Database()->select('extensions')
      ->fields(NULL, ['name', 'type'])
      ->condition('installed', '1');

    /** @var Result $extensions_result */
    if (!$extensions_result = $extensions_storage->execute()) {
      return [];
    }

    return $extensions_result->fetchAllAssoc() ?? [];
  }

  /**
   * @param $extension
   *
   * @return bool
   */
  public static function extensionInstalled($extension) {
    $extensions = self::getInstalledExtensions();
    foreach ($extensions as $ext) {
      if ($ext['name'] === $extension) {
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
    }, glob('core/components/*', GLOB_ONLYDIR));
  }

  /**
   * @return array
   */
  protected static function getContribExtensions() {
    return array_map(function ($item) {
      return str_replace('extensions/', '', $item);
    }, glob('extensions/*', GLOB_ONLYDIR));
  }

  /**
   * @return array
   */
  protected static function getAllMatterClasses() {
    $matters = [];
    $extensionsChecked = [];
    $extensions = self::getCoreExtensions() + self::getContribExtensions();
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
   * Returns the information in the extension.yml file.
   *
   * @param string $extension
   *
   * @return bool|mixed
   */
  public static function getExtensionInfo($extension) {
    return YamlReader::readExtension($extension);
  }

  /**
   * Generates random uuid.
   *
   * @return string
   */
  public static function createUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000,
      mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }

  /**
   * Returns currently active Cache class.
   *
   * @return CacheInterface
   */
  public static function getCacheClass() {
    // @TODO: dynamically return the currently active cache class!
    return new Cache;
  }

}
