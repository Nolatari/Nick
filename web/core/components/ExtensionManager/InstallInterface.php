<?php

namespace Nick\ExtensionManager;

interface InstallInterface {

  /**
   * Checks whether the doInstall function has already been used.
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