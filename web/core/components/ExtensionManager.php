<?php

namespace Nick;

use Nick;
use Nick\Database\Result;

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