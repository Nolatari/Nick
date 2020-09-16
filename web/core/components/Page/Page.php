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
  public function getCacheOptions(): array {
    return $this->caching;
  }

  /**
   * @inheritDoc
   */
  public function get($parameter): ?string {
    return $this->parameters[$parameter] ?? NULL;
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
   * Checks if parameter exists.
   *
   * @param string $parameter
   *
   * @return bool
   */
  protected function hasParameter(string $parameter): bool {
    return $this->get($parameter) !== NULL;
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
  protected function setParameter(string $key, string $value) {
    $this->parameters[$key] = $value;
  }

  /**
   * Clones a parameter to a different key.
   *
   * @param string $originalKey
   * @param string $cloneKey
   */
  protected function cloneParameter(string $originalKey, string $cloneKey) {
    $this->setParameter($cloneKey, $this->get($originalKey));
  }

}
