<?php

namespace Nick\Rest\Pages;

use Nick\Page\Page;
use Nick\Rest\Rest;

/**
 * Class Transmit
 *
 * @package Nick\Rest\Pages
 */
class Transmit extends Page {

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'rest.transmit',
      'context' => 'page',
      'max-age' => 0,
    ];
  }

  public function render() {
    parent::render();
    Rest::Transmit($parameters);
  }

}
