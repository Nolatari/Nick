<?php

namespace Nick\ExtensionManager;

interface UninstallInterface {

  /**
   * Perform uninstall actions (Remove matter types, ..)
   *
   * @return bool
   */
  public function doUninstall();

}