<?php

namespace Nick\Pages;

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
    parent::render();
    if (in_array('clear_all', $parameters)) {
      \Nick::Cache()->clearAllCaches();
    } else {
      // @TODO
    }
  }

}