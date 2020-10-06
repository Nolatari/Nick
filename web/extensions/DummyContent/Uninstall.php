<?php

namespace Nick\DummyContent;

use Nick;
use Nick\ExtensionManager\InstallInterface;
use Nick\ExtensionManager\UninstallInterface;
use Nick\Menu\MenuInterface;
use Nick\Page\PageManager;

/**
 * Class Uninstall
 *
 * @package Nick\DummyContent
 */
class Uninstall implements UninstallInterface {

  /** @var PageManager $pageManager */
  protected PageManager $pageManager;

  /**
   * install constructor.
   */
  public function __construct() {
    $this->pageManager = new PageManager();
  }

  /**
   * Checks whether the page already exists
   *
   * @return bool
   */
  public function condition(): bool {
    $pageObject = $this->pageManager->getPageObject('dummycontent');
    if ($pageObject instanceof DummyContent) {
      return FALSE;
    }

    return TRUE;
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
        'route' => 'dummycontent'
      ]);
    if (!$menu->delete()) {
      return FALSE;
    }

    $page = Nick::Database()
      ->delete('pages')
      ->condition('id', 'dummycontent');
    if (!$page->execute()) {
      return FALSE;
    }

    return TRUE;
  }

}
