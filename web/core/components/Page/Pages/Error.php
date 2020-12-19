<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Error
 *
 * @package Nick\Page
 */
class Error extends Page {

  /**
   * Error constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'error',
      'title' => $this->translate('Error'),
      'summary' => $this->translate('There was an error trying to reach a certain page.'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'page.error',
      'context' => 'page',
      'max-age' => 0,
    ];

    if ($this->hasParameter('key')) {
      $this->caching['key'] .= '.' . $this->get('key');
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();
    switch ($this->get('key')) {
      case '404':
        $title = 'Page not found';
        break;
      case '403':
        $title = 'Forbidden';
        break;
      case '301':
        $title = 'Moved permanently';
        break;
      default:
        $this->setParameter('key', 500);
        $title = 'Internal server error';
        break;
    }

    $this->setParameter('page.title', $title);
    return \Nick::Renderer()
      ->setType('error')
      ->setTemplate($this->get('key'))
      ->render($this->getParameters());
  }

}