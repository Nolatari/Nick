<?php

namespace Nick\Cache\Pages;

use Nick;
use Nick\Logger;
use Nick\Page\Page;
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
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Cache\\Pages\\Cache',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    if (isset($parameters['t']) && $parameters['t'] == 'clear_all') {
      if (Nick::Cache()->clearAllCaches() !== FALSE) {
        Nick::Logger()->add('Successfully cleared all caches.', Logger::TYPE_SUCCESS, 'Cache');
      } else {
        Nick::Logger()->add('Could not clear caches.', Logger::TYPE_FAILURE, 'Cache');
      }

      header('Location: ' . Url::fromRoute([
          $parameters['data']['p'],
          $parameters['data']['t'],
          $parameters['data']['id'],
        ]));
    } else {
      // @TODO
    }
  }

}