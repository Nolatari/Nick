<?php

namespace Nick\Article;

use Nick\ExtensionManager\UninstallInterface;
use Nick\Menu\MenuInterface;

/**
 * Class Uninstall
 *
 * @package Nick\Article
 */
class Uninstall implements UninstallInterface {

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function condition() {
    $overview = \Nick::EntityManager()
      ->loadByProperties([
        'type' => 'menu',
        'route' => 'article.overview'
      ]);
    return $overview === FALSE;
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function doUninstall() {
    /** @var MenuInterface $menu */
    $menu = \Nick::EntityManager()
      ->loadByProperties([
        'type' => 'menu',
        'route' => 'article.overview'
      ]);
    $menu->delete();

    \Nick::EntityManager()::uninstallEntityType('article');
  }

}
