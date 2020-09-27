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
   * Header constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'header',
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
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
  public function render(&$parameters = []) {
    parent::render($parameters);
    return Nick::Renderer()
      ->setType()
      ->setTemplate('header')
      ->render($parameters ?? NULL);
  }

}