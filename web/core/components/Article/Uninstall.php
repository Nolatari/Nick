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
   *
   * @throws \Exception
   */
  public function condition() {
    $overview = \Nick::MatterManager()
      ->loadByProperties([
        'type' => 'menu',
        'route' => 'article.overview'
      ]);
    return $overview === FALSE;
  }

  /**
   * @inheritDoc
   *
   * @throws \Exception
   */
  public function doUninstall() {
    /** @var MenuInterface $menu */
    $menu = \Nick::MatterManager()
      ->loadByProperties([
        'type' => 'menu',
        'route' => 'article.overview'
      ]);
    $menu->delete();
    \Nick::Logger()->add('Removed menu item', Logger::TYPE_INFO, 'Article');
    \Nick::Database()->delete('matter')
      ->condition('type', 'article')
      ->execute();
    \Nick::Database()->delete('matter_storage')
      ->condition('type', 'article')
      ->execute();
    \Nick::Database()->query('DROP TABLE matter__article');
    \Nick::Logger()->add('Removed Article matters', Logger::TYPE_INFO, 'Article');
  }

}
