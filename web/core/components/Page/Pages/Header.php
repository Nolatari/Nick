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
   * {@inheritDoc}
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'header',
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'page.header',
      'context' => 'page',
      'tags' => ['header'],
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('header')
      ->render($this->getParameters() ?? []);
  }

}