<?php

namespace Nick\Cache\Pages;

use Nick;
use Nick\Logger;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Cache
 *
 * @package Nick\Cache\Pages
 */
class Cache extends Page {

  /**
   * Cache constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'cache',
      'title' => 'Cache',
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'page.cache',
      'context' => 'page',
      'tags' => ['cache'],
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();
    if ($this->hasParameter(1) && $this->get(1) === 'clear') {
      if (\Nick::Cache()->clearAllCaches() !== FALSE) {
        \Nick::Logger()->add('Successfully cleared all caches.', Logger::TYPE_SUCCESS, 'Cache');
      } else {
        \Nick::Logger()->add('Could not clear caches.', Logger::TYPE_FAILURE, 'Cache');
      }

      \Nick::Init(Request::createFromGlobals());

      $response = new RedirectResponse(Url::fromRoute(\Nick::Route()->load('dashboard')));
      $response->send();
    }
  }

}