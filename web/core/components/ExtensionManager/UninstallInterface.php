<?php

namespace Nick\ExtensionManager;

/**
 * Interface UninstallInterface
 *
 * @package Nick\ExtensionManager
 */
interface UninstallInterface {

  /**
   * Checks whether the doUninstall function should be used.
   *
   * @return bool
   */
  public function condition();

  /**
   * Perform uninstall actions (Remove entity types, pages, menu items, ...)
   *
   * @return bool
   */
  public function doUninstall();

}