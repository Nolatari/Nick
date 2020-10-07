<?php

namespace Nick\Conversation;

use Nick\ExtensionManager\InstallInterface;
use Nick\Logger;

/**
 * Class Install
 *
 * @package Nick\Conversation
 */
class Install implements InstallInterface {

  /**
   * {@inheritDoc}
   */
  public function condition() {
    return \Nick::ExtensionManager()::extensionInstalled('Message');
  }

  /**
   * {@inheritDoc}
   */
  public function doInstall() {
    if (!\Nick::ExtensionManager()::installExtension('Message', 'core')) {
      return FALSE;
    }
    \Nick::Logger()->add('Installed Message as subextension', Logger::TYPE_INFO, 'Conversation');

    return TRUE;
  }
}