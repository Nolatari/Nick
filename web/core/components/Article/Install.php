<?php

namespace Nick\Article;

use Nick\ExtensionManager\InstallInterface;
use Nick\Logger;
use Nick\Menu\Menu;

/**
 * Class Install
 *
 * @package Nick\Article
 */
class Install implements InstallInterface {

  /**
   * @inheritDoc
   *
   * @throws \Exception
   */
  public function condition() {
    $overview = \Nick::MatterManager()->loadByProperties(['type' => 'menu', 'route' => 'article.overview'], TRUE);
    return $overview !== FALSE;
  }

  /**
   * @inheritDoc
   */
  public function doInstall() {
    $menu = new Menu([
      'route' => 'article.overview',
      'title' => 'Articles',
      'description' => 'Overview of all articles',
      'structure' => 0,
      'type' => 'link',
      'translatable' => TRUE,
      'parent' => 0,
      'owner' => 1,
      'status' => 1,
    ]);
    $menu->save();
    \Nick::Logger()->add('Added menu item', Logger::TYPE_INFO, 'Article');
  }
}