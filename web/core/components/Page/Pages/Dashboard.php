<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\ExtensionManager;

/**
 * Class Dashboard
 *
 * @package Nick\Page
 */
class Dashboard extends Page {

  /**
   * Dashboard constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->setParameters([
      'id' => 'dashboard',
      'title' => $this->translate('Dashboard'),
      'summary' => $this->translate('Welcome to your Nick Dashboard!'),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.dashboard',
      'context' => 'page',
      'max-age' => 1800,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);
    return Nick::Renderer()
      ->setType()
      ->setTemplate('dashboard')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'dashboard' => [
          'installedExtensions' => count(ExtensionManager::getInstalledExtensions()),
        ],
      ]);
  }

}