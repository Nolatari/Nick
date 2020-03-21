<?php

namespace Nick\Pages;

use Nick\Logger;

/**
 * Class Cache
 *
 * @package Nick\Pages
 */
class Cache extends Pages {

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
    if (in_array('clear_all', $parameters)) {
      if (\Nick::Cache()->clearAllCaches() !== FALSE) {
        \Nick::Logger()->add('Successfully cleared all caches.', Logger::TYPE_SUCCESS, 'Cache');
      } else {
        \Nick::Logger()->add('Could not clear caches.', Logger::TYPE_FAILURE, 'Cache');
      }
      header ('Location: ./');
    } else {
      // @TODO
    }
  }

}