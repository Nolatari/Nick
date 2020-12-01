<?php

namespace Nick\Rest\Pages;

use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Retrieve
 * @package Nick\Rest\Pages
 */
class Retrieve extends Page {

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'rest.retrieve',
      'context' => 'page',
      'max-age' => 0,
    ];
  }

  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $response = new JsonResponse();
    $response->setContent(json_encode(['parameters' => $parameters['param']]));
    $response->send();
  }

}
