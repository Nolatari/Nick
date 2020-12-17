<?php

namespace Nick\Rest\Pages;

use Nick\Page\Page;
use Nick\Rest\Entity\Client;
use Nick\Rest\Rest;
use Nick\Route\RouteInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Clients
 * @package Nick\Rest\Pages
 */
class Clients extends Page {

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'rest.clients',
      'context' => 'page',
      'tags' => ['clients.overview'],
      'max-age' => 300,
    ];

    return $this;
  }

  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $clients = Client::loadMultiple();
    d($clients);
  }

}
