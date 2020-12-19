<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Element;
use Nick\Route\RouteInterface;

/**
 * Class Header_Element
 *
 * @package Nick\Page
 */
class Header_Element extends Element {

  /**
   * Header_Element constructor.
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
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'element.header',
      'context' => 'element',
      'tags' => ['header'],
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