<?php

namespace Nick\Cache\Pages;

use Nick;
use Nick\Logger;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\Url;

/**
 * Class Cache
 *
 * @package Nick\Cache\Pages
 */
class Cache extends Page {

  /**
   * Cache constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'cache',
      'title' => 'Cache',
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.cache',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    if (isset($parameters[2]) && $parameters[2] === 'clear') {
      if (Nick::Cache()->clearAllCaches() !== FALSE) {
        Nick::Logger()->add('Successfully cleared all caches.', Logger::TYPE_SUCCESS, 'Cache');
      } else {
        Nick::Logger()->add('Could not clear caches.', Logger::TYPE_FAILURE, 'Cache');
      }

      header('Location: ' . Url::fromRoute(Nick::Route()->load('dashboard')));
    }
  }

}