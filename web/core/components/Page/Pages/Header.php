<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

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
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('header')
      ->render($parameters ?? NULL);
  }

}