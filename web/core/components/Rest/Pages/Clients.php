<?php

namespace Nick\Rest\Pages;

use Nick\Page\Page;
use Nick\Rest\Entity\Client;
use Nick\Route\RouteInterface;

/**
 * Class Clients
 *
 * @package Nick\Rest\Pages
 */
class Clients extends Page {

  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'rest.clients',
      'title' => $this->translate('Rest Clients'),
      'summary' => $this->translate('Welcome to your Nick Dashboard!'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'rest.clients',
      'context' => 'page',
      'tags' => ['entity:client:overview'],
      'max-age' => 900,
    ];

    return $this;
  }

  /**
   * @param array          $parameters
   * @param RouteInterface $route
   *
   * @return string|void|NULL
   */
  public function render() {
    parent::render();

    $clients = Client::loadMultiple();
    d($clients);

    return NULL;
  }

}
