<?php

namespace Nick\Page\Elements;

use Nick;
use Nick\Page\Element;
use Nick\Route\RouteInterface;

/**
 * Class Header
 *
 * @package Nick\Elements
 */
class Header extends Element {

  /**
   * Header constructor.
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
      'key' => 'element.header',
      'context' => 'element',
      'tags' => ['element:header'],
      'max-age' => 900,
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
      ->render($parameters ?? NULL);
  }

}