<?php

namespace Nick\Page;

use Nick\Route\RouteInterface;
use Nick\Translation\StringTranslation;
use Nick\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Element
 *
 * @package Nick\Page
 */
class Element implements ElementInterface {
  use StringTranslation;

  /** @var array $caching */
  protected array $caching;

  /** @var array $parameters */
  protected array $parameters = [];

  /** @var array $permissions */
  protected array $permissions = [];

  /**
   * Element constructor.
   */
  public function __construct() {
    // TODO: Set fallback parameters
    $this->setCacheOptions(Url::getParameters());
  }

  /**
   * {@inheritDoc}
   */
  public function getPermissions(): array {
    return $this->permissions;
  }

  /**
   * Sets permissions required to view this element
   *
   * @param array $permissions
   *
   * @return Page
   */
  protected function setPermissions(array $permissions): self {
    $this->permissions = $permissions;
    return $this;
  }

  /**
   * Sets caching for element.
   *
   * @param array|null $parameters
   *
   * @return self
   */
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'element',
      'tags' => ['element'],
      'context' => ['element'],
      'max-age' => -1,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheOptions(): array {
    return $this->caching;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    \Nick::Event('elementPreRender')
      ->fire($parameters, [$this->get('id')]);

    foreach ($this->getPermissions() as $permission) {
      if (!\Nick::CurrentPerson()->hasPermission($permission)) {
        /** @var RouteInterface $dashboard */
        $dashboard = \Nick::Route()->load('dashboard');
        $redirect = new RedirectResponse($dashboard->getUrl());
        $redirect->setStatusCode(401);
        $redirect->send();
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function get(string $parameter): ?string {
    return $this->parameters[$parameter] ?? NULL;
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
   * Sets element parameters
   *
   * @param array $parameters
   */
  protected function setParameters($parameters = []) {
    $this->parameters = $this->parameters + $parameters;
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

  /**
   * @param string $key
   * @param string $value
   */
  protected function setParameter(string $key, string $value) {
    $this->parameters[$key] = $value;
  }

}
