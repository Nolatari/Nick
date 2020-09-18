<?php

namespace Nick\ExtensionManager;

use Nick;
use Nick\Database\Result;
use Nick\YamlReader;

/**
 * Class ExtensionManager
 *
 * @package Nick
 */
class ExtensionManager {

  /**
   * @return array|bool
   */
  public static function getInstalledExtensions() {
    $extensions_storage = Nick::Database()->select('extensions')
      ->fields(NULL, ['name', 'type'])
      ->condition('installed', '1');

    /** @var Result $extensions */
    if (!$extensions = $extensions_storage->execute()) {
      return [];
    }

    return $extensions->fetchAllAssoc() ?? [];
  }

  /**
   * Install an extension.
   *
   * @param string $extension
   * @param string $type
   *
   * @return bool
   */
  public static function installExtension(string $extension, string $type): bool {
    // @TODO: Validate extension
    $extension_storage = Nick::Database()->insert('extensions')
      ->values(['installed' => 1, 'name' => $extension, 'type' => $type]);
    if (!$extension_storage->execute()) {
      return FALSE;
    }

    $installObjectName = '\\Nick\\' . $extension . '\\Install';
    if (class_exists($installObjectName)) {
      $installObject = new $installObjectName();

      if (!$installObject->condition()) {
        try {
          $installObject->doInstall();
        } catch(\Exception $e) {
          Nick::Logger()->add($e->getMessage());
        }
      }
    }

    return TRUE;
  }

  public static function installExtensions() {
    $extensions = self::getInstalledExtensions();
    foreach ($extensions as $extension) {
      $installObjectName = '\\Nick\\' . $extension['name'] . '\\Install';
      if (!class_exists($installObjectName)) {
        continue;
      }
      /** @var InstallInterface $installObject */
      $installObject = new $installObjectName();

      if (!$installObject->condition()) {
        try {
          $installObject->doInstall();
        } catch(\Exception $e) {
          Nick::Logger()->add($e->getMessage());
        }
      }
    }
  }

  /**
   * @param string $extension
   *
   * @return bool
   */
  public static function extensionInstalled(string $extension) {
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
  public static function getCoreExtensions() {
    return array_map(function ($item) {
      return str_replace('core/components/', '', $item);
    }, glob('core/components/*', GLOB_ONLYDIR));
  }

  /**
   * @return array
   */
  public static function getContribExtensions() {
    return array_map(function ($item) {
      return str_replace('extensions/', '', $item);
    }, glob('extensions/*', GLOB_ONLYDIR));
  }

  /**
   * Returns the information in the extension.yml file.
   *
   * @param string $extension
   *
   * @return bool|mixed
   */
  public static function getExtensionInfo(string $extension) {
    return YamlReader::readExtension($extension);
  }

}