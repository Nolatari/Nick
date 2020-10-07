<?php

namespace Nick\Message;

use Nick\ExtensionManager\UninstallInterface;

/**
 * Class Uninstall
 *
 * @package Nick\Message
 */
class Uninstall implements UninstallInterface {

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function condition() {
    return \Nick::EntityManager()::entityInstalled('message') === FALSE;
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function doUninstall() {
    \Nick::EntityManager()::uninstallEntityType('message');
    \Nick::ExtensionManager()::uninstallExtension('Conversation');
  }

}
