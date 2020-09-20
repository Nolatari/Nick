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
   * @param string $extension
   *
   * @return bool
   */
  public static function uninstallExtension(string $extension): bool {
    // @TODO: Validate extension, skip required extensions
    $extension_storage = Nick::Database()->update('extensions')
      ->condition('installed', '1')
      ->condition('name', $extension)
      ->values(['installed' => 0]);
    if (!$extension_storage->execute()) {
      return FALSE;
    }

    $uninstallObjectName = '\\Nick\\' . $extension . '\\Uninstall';
    if (class_exists($uninstallObjectName)) {
      /** @var UninstallInterface $uninstallObject */
      $uninstallObject = new $uninstallObjectName();

      try {
        $uninstallObject->doUninstall();
      } catch(\Exception $e) {
        Nick::Logger()->add($e->getMessage());
      }
    }

    Nick::Logger()->add('Uninstalled extension ' . $extension, Nick::Logger()::TYPE_SUCCESS, 'Extensions');
    return TRUE;
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
    // @TODO: Validate extension, check compatibility
    $extension_storage = Nick::Database()->select('extensions')
      ->condition('name', $extension);
    $result = $extension_storage->execute();
    if (!$result instanceof Result) {
      return FALSE;
    }

    if ($result->getCount() == 1) {
      $ext = Nick::Database()->update('extensions')
        ->values(['installed' => 1])
        ->condition('name', $extension);
    } else {
      $ext = Nick::Database()->insert('extensions')
        ->values([
          'type' => $type,
          'name' => $extension,
          'installed' => 1,
        ]);
    }
    if (!$ext->execute()) {
      return FALSE;
    }

    $installObjectName = '\\Nick\\' . $extension . '\\Install';
    if (class_exists($installObjectName)) {
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

    Nick::Logger()->add('Installed extension ' . $extension, Nick::Logger()::TYPE_SUCCESS, 'Extensions');
    return TRUE;
  }

  /**
   * Initiates Install functions on installed extensions.
   */
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

  /**
   * Checks whether extension is latest version
   *
   * @param string $extension
   *
   * @return bool
   */
  public static function isLatestVersion(string $extension) {
    // TODO
    return TRUE;
  }

}