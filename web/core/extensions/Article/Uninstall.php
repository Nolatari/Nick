<?php

namespace Nick\Article;

use Nick\ExtensionManager\UninstallInterface;
use Nick\Menu\Entity\MenuInterface;

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
    return $overview === FALSE && \Nick::EntityManager()::entityInstalled('article');
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
    if ($menu !== FALSE) {
      $menu->delete();
    }

    \Nick::EntityManager()::uninstallEntityType('article');
  }

}
