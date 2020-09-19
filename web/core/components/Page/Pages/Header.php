<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;

/**
 * Class Header
 *
 * @package Nick\Page
 */
class Header extends Page {

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.header',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Page\\Pages\\Header',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    return Nick::Renderer()
      ->setType()
      ->setTemplate('header')
      ->render($parameters ?? NULL);
  }

}