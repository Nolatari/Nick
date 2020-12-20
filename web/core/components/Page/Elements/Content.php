<?php

namespace Nick\Page\Elements;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Content
 *
 * @package Nick\Page\Elements
 */
class Content extends Page {

  /**
   * Content constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'content',
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'element.content',
      'context' => 'element',
      'tags' => ['element:content'],
      'max-age' => 3600,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    if (parent::render() === NULL) {
      return '';
    }

    return $this->getRoute()->render();
  }

}
