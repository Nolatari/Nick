<?php

namespace Nick\Conversation;

use Nick\ExtensionManager\UninstallInterface;

/**
 * Class Uninstall
 *
 * @package Nick\Conversation
 */
class Uninstall implements UninstallInterface {

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function condition() {
    return \Nick::EntityManager()::entityInstalled('conversation') === FALSE;
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function doUninstall() {
    \Nick::EntityManager()::uninstallEntityType('conversation');
    \Nick::EntityManager()::uninstallEntityType('message');
  }

}
