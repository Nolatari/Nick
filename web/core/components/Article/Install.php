<?php

namespace Nick\Article;

use Nick\ExtensionManager\InstallInterface;
use Nick\Logger;
use Nick\Menu\Menu;
use Nick\Page\PageManager;

/**
 * Class Install
 *
 * @package Nick\Article
 */
class Install implements InstallInterface {

  /** @var PageManager $pageManager */
  protected PageManager $pageManager;

  /**
   * install constructor.
   */
  public function __construct() {
    $this->pageManager = \Nick::PageManager();
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function condition() {
    $overview = \Nick::EntityManager()->loadByProperties(['type' => 'menu', 'route' => 'article.overview'], TRUE);
    return $overview !== FALSE;
  }

  /**
   * {@inheritDoc}
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
    if (!$menu->save()) {
      return FALSE;
    }
    \Nick::Logger()->add('Added menu item', Logger::TYPE_INFO, 'Article');

    if (!$this->pageManager->createPage([
      'article',
      '\\Nick\\Article\\Article',
    ])) {
      return FALSE;
    }
    return TRUE;
  }
}