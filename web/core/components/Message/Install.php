<?php

namespace Nick\Message;

use Nick\ExtensionManager\InstallInterface;
use Nick\Logger;

/**
 * Class Install
 *
 * @package Nick\Message
 */
class Install implements InstallInterface {

  /**
   * {@inheritDoc}
   */
  public function condition() {
    return \Nick::ExtensionManager()::extensionInstalled('Conversation');
  }

  /**
   * {@inheritDoc}
   */
  public function doInstall() {
    if (!\Nick::ExtensionManager()::installExtension('Conversation', 'core')) {
      return FALSE;
    }
    \Nick::Logger()->add('Installed Conversation as parent module', Logger::TYPE_INFO, 'Message');

    return TRUE;
  }
}