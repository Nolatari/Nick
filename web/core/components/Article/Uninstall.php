<?php

namespace Nick\Article;

use Nick\ExtensionManager\UninstallInterface;
use Nick\Logger;
use Nick\Menu\MenuInterface;

/**
 * Class Uninstall
 *
 * @package Nick\Article
 */
class Uninstall implements UninstallInterface {

  /**
   * @inheritDoc
   */
  public function condition() {
    $overview = \Nick::MatterManager()->loadByProperties(['type' => 'menu', 'route' => 'article.overview']);
    return $overview === FALSE;
  }

  /**
   * @inheritDoc
   */
  public function doUninstall() {
    /** @var MenuInterface $menu */
    $menu = \Nick::MatterManager()->loadByProperties(['type' => 'menu', 'route' => 'article.overview']);
    $menu->delete();
    \Nick::Logger()->add('Removed menu item', Logger::TYPE_INFO, 'Article');
  }
}