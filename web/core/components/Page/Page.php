<?php

namespace Nick\Page;

use Nick\Event\Event;
use Nick\Translation\StringTranslation;

/**
 * Class Dashboard
 *
 * @package Nick\Page
 */
class Page implements PageInterface {
  use StringTranslation;

  /** @var array $caching */
  protected $caching;

  /** @var array $parameters */
  protected $parameters;

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
   * @inheritDoc
   */
  public function get($parameter) {
    return $this->parameters[$parameter] ?? '';
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    $event = new Event('pagePreRender');
    $event->fire($parameters);
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
   * Sets page parameters
   *
   * @param array $parameters
   */
  protected function setParameters($parameters = []) {
    $this->parameters = $parameters;
  }

  /**
   * @param string $key
   * @param string $value
   */
  protected function setParameter($key, $value) {
    $this->parameters[$key] = $value;
  }

}
