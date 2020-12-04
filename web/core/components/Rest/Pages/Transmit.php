<?php

namespace Nick\Rest\Pages;

use Nick\Page\Page;
use Nick\Rest\Rest;
use Nick\Route\RouteInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Transmit
 * @package Nick\Rest\Pages
 */
class Transmit extends Page {

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'rest.transmit',
      'context' => 'page',
      'max-age' => 0,
    ];
  }

  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    Rest::Transmit($parameters);
  }

}
