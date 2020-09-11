<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Logger;
use Nick\Page\Page;

/**
 * Class Cache
 *
 * @package Nick\Page
 */
class Cache extends Page {

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
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
  public function render($parameters = []) {
    if (isset($parameters['clear_all'])) {
      if (Nick::Cache()->clearAllCaches() !== FALSE) {
        Nick::Logger()->add('Successfully cleared all caches.', Logger::TYPE_SUCCESS, 'Cache');
      } else {
        Nick::Logger()->add('Could not clear caches.', Logger::TYPE_FAILURE, 'Cache');
      }
      header('Location: ./');
    } else {
      // @TODO
    }
  }

}