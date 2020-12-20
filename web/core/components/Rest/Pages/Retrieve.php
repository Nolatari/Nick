<?php

namespace Nick\Rest\Pages;

use Nick\Page\Page;
use Nick\Rest\Rest;

/**
 * Class Retrieve
 *
 * @package Nick\Rest\Pages
 */
class Retrieve extends Page {

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'rest.retrieve',
      'context' => 'page',
      'max-age' => 0,
    ];
  }

  public function render() {
    parent::render();

    Rest::Retrieve($parameters);
  }

}
