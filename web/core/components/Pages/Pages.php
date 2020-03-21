<?php

namespace Nick\Pages;

/**
 * Class Dashboard
 *
 * @package Nick\Pages
 */
class Pages implements PagesInterface {

  /** @var array $caching */
  protected $caching;

  /**
   * Dashboard constructor.
   */
  public function __construct() {
    $this->setCacheOptions();
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheOptions() {
    return $this->caching;
  }

  /**
   * Sets caching for page.
   *
   * @return $this
   */
  protected function setCacheOptions() {
    $this->caching = [
      'tags' => [],
      'context' => [],
      'max-age' => -1,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    global $variables;
  }

}