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
  public function __construct() {
    $this->setParameters([
      'id' => 'error',
      'title' => $this->translate('Error'),
      'summary' => $this->translate('There was an error trying to reach a certain page.'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.error',
      'context' => 'page',
      'max-age' => -1,
    ];

    if (isset($parameters[2])) {
      $this->caching['key'] = $this->caching['key'] . '.' . $parameters[2];
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    switch ($parameters[2]) {
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
        $parameters[2] = '500';
        $title = 'Internal server error';
        break;
    }

    $parameters['page']['title'] = $title;
    return Nick::Renderer()
      ->setType('error')
      ->setTemplate($parameters[2])
      ->render($parameters);
  }

}