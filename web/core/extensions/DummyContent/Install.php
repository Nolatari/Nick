<?php

namespace Nick\DummyContent;

use Nick\ExtensionManager\InstallInterface;
use Nick\Logger;
use Nick\Menu\Entity\Menu;

/**
 * Class Install
 *
 * @package Nick\DummyContent
 */
class Install implements InstallInterface {

  /**
   * Checks whether the page already exists
   *
   * @return bool
   */
  public function condition(): bool {
    $pageObject = \Nick::PageManager()->getPageObject('dummycontent');
    if (!$pageObject instanceof DummyContent) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Creates page in database.
   *
   * @return bool
   */
  public function doInstall(): bool {
    $menu = new Menu([
      'route' => 'dummycontent',
      'title' => 'Dummy Content',
      'description' => 'Create dummy content',
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
    \Nick::Logger()->add('Added menu item for DummyContent', Logger::TYPE_INFO, 'DummyContent');

    if (!\Nick::PageManager()->createPage([
      'dummycontent',
      '\\Nick\\DummyContent\\DummyContent',
    ])) {
      return FALSE;
    }

    return TRUE;
  }

}