<?php

namespace Nick\DummyContent;

use Nick\ExtensionManager\InstallInterface;
use Nick\Page\PageManager;

/**
 * Class Install
 *
 * @package Nick\DummyContent
 */
class Install implements InstallInterface {

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
    return $this->pageManager->createPage([
      'dummycontent',
      '\\Nick\\DummyContent\\DummyContent',
    ]);
  }

}