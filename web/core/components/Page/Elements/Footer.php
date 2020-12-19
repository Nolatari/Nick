<?php

namespace Nick\Page\Elements;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Footer
 *
 * @package Nick\Elements
 */
class Footer extends Page {

  /**
   * Footer constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'footer',
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'element.footer',
      'context' => 'element',
      'tags' => ['element:footer'],
      'max-age' => 3600,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('footer')
      ->render();
  }

}