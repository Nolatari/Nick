<?php

namespace Nick\ExtensionManager;

/**
 * Interface InstallInterface
 *
 * @package Nick\ExtensionManager
 */
interface InstallInterface {

  /**
   * Checks whether the doInstall function has already been used.
   * If it returns FALSE, then the conditions for this extension are NOT met and doInstall() will be used.
   *
   * @return bool
   */
  public function condition();

  /**
   * Execute this function to install any necessary features before using the extension.
   *
   * @return bool
   */
  public function doInstall();

}