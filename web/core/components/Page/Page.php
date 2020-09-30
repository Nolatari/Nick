<?php

namespace Nick\Page;

use Nick\Event\Event;
use Nick\Translation\StringTranslation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Dashboard
 *
 * @package Nick\Page
 */
class Page implements PageInterface {
  use StringTranslation;

  /** @var array $caching */
  protected array $caching;

  /** @var array $parameters */
  protected array $parameters = [];

  /**
   * Dashboard constructor.
   */
  public function __construct() {
    $this->setCacheOptions($_GET);
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
  public function render(&$parameters = []) {
    $event = new Event('pagePreRender');
    $event->fire($parameters, [$this->get('id')]);
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    // Empty function, has to be overwritten in child class.
  }

  /**
   * Sets caching for page.
   *
   * @param array|null $parameters
   *
   * @return self
   */
  protected function setCacheOptions($parameters = []) {
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
    $this->parameters = $this->parameters + $parameters;
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
    $this->setParameter($cloneKey, $this->get($originalKey) ?? '');
  }

}
