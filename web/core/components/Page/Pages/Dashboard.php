<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Dashboard
 *
 * @package Nick\Page
 */
class Dashboard extends Page {

  /**
   * {@inheritDoc}
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'dashboard',
      'title' => $this->translate('Dashboard'),
      'summary' => $this->translate('Welcome to your Nick Dashboard!'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'page.dashboard',
      'context' => 'page',
      'tags' => ['dashboard'],
      'max-age' => 1800,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('dashboard')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'dashboard' => [
          'installedExtensions' => count(\Nick::ExtensionManager()::getInstalledExtensions()),
        ],
      ]);
  }

}