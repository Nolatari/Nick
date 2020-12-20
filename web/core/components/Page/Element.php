<?php

namespace Nick\Page;

use Nick\Route\RouteInterface;
use Nick\StringManipulation;
use Nick\Translation\StringTranslation;

/**
 * Class Element
 *
 * @package Nick\Element
 */
class Element implements ElementInterface {
  use StringTranslation;

  /** @var array $caching */
  protected array $caching;

  /** @var array $parameters */
  protected array $parameters = [];

  /** @var RouteInterface $route */
  protected RouteInterface $route;

  /** @var array $permissions */
  protected array $permissions = [];

  /**
   * Element constructor.
   *
   * @param array          $parameters
   * @param RouteInterface $route
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters($parameters);
    $this->setRoute($route);
    $this->setCacheOptions();
  }

  /**
   * {@inheritDoc}
   */
  public function getPermissions(): array {
    return $this->permissions;
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
      ->fire($this->parameters, [$this->get('id')]);

    foreach ($this->getPermissions() as $permission) {
      if (!\Nick::CurrentPerson()->hasPermission($permission)) {
        return NULL;
      }
    }

    return '';
  }

  /**
   * {@inheritDoc}
   */
  public function get(string $parameter): ?string {
    return $this->parameters[$parameter] ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getParameters() {
    return $this->parameters;
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
   *
   * @return Element
   */
  protected function setParameters($parameters = []): self {
    $this->parameters = $this->parameters + $parameters;
    return $this;
  }

  /**
   * Clones a parameter to a different key.
   *
   * @param string $originalKey
   * @param string $cloneKey
   *
   * @return self
   */
  protected function cloneParameter(string $originalKey, string $cloneKey): self {
    return $this->setParameter($cloneKey, $this->get($originalKey) ?? '');
  }

  /**
   * @param string $key
   * @param string $value
   *
   * @return self
   */
  protected function setParameter(string $key, string $value): self {
    if (StringManipulation::contains($key, '.')) {
      $keys = StringManipulation::explode($key, '.');
      $key = end($keys);
      $return = &$this->parameters;
      foreach ($keys as $item) {
        $return = &$return[$item];
        if ($item === $key) {
          $return = $value;
        }
      }
      return $this;
    }
    $this->parameters[$key] = $value;
    return $this;
  }

  /**
   * @param RouteInterface $route
   *
   * @return self
   */
  protected function setRoute(RouteInterface $route): self {
    $this->route = $route;
    return $this;
  }

  /**
   * @return RouteInterface
   */
  protected function getRoute(): RouteInterface {
    return $this->route;
  }

  /**
   * Adds Element to list of elements.
   *
   * @param ElementInterface $element
   *
   * @return self
   */
  protected function addElement(ElementInterface $element): self {
    $this->elements[] = $element;
    return $this;
  }

  /**
   * Returns rendered string of elements
   *
   * @return array
   */
  protected function getRenderedElements(): array {
    $elements = [];
    /** @var ElementInterface $element */
    foreach ($this->getElements() as $element) {
      $elements[$element->get('id')] = $element->render();
    }

    return $elements;
  }

  /**
   * Sets permissions required to view this element
   *
   * @param array $permissions
   *
   * @return Element
   */
  protected function setPermissions(array $permissions): self {
    $this->permissions = $permissions;
    return $this;
  }

  /**
   * Sets caching for element.
   *
   * @return self
   */
  protected function setCacheOptions(): self {
    $this->caching = [
      'key' => 'element',
      'tags' => ['element'],
      'context' => ['element'],
      'max-age' => -1,
    ];

    return $this;
  }

}
