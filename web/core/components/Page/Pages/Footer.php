<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Footer
 *
 * @package Nick\Page
 */
class Footer extends Page {

  /**
   * {@inheritDoc}
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
      'key' => 'page.footer',
      'context' => 'page',
      'tags' => ['footer'],
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
      ->render($this->getParameters());
  }

}