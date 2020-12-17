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

  public function __construct() {
    $this->setParameters([
      'id' => 'rest.clients',
      'title' => $this->translate('Rest Clients'),
      'summary' => $this->translate('Welcome to your Nick Dashboard!'),
    ]);
    parent::__construct();
  }

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

  /**
   * @param array $parameters
   * @param RouteInterface $route
   *
   * @return string|void|NULL
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $clients = Client::loadMultiple();
    d($clients);exit;
  }

}
