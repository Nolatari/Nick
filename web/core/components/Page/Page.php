<?php

namespace Nick\Page;

use Nick\Event\Event;
use Nick\Route\RouteInterface;
use Nick\StringManipulation;
use Nick\Translation\StringTranslation;
use Nick\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Page
 *
 * @package Nick\Page
 */
class Page implements PageInterface {
  use StringTranslation;

  /** @var array $caching */
  protected array $caching;

  /** @var array $parameters */
  protected array $parameters = [];

  /** @var RouteInterface $route */
  protected RouteInterface $route;

  /** @var array $permissions */
  protected array $permissions = [];

  /** @var array $elements */
  protected array $elements = [];

  /**
   * Page constructor.
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
   * @return array
   */
  public function getElements(): array {
    return $this->elements;
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
    \Nick::Event('pagePreRender')
      ->fire($this->parameters, [$this->get('id')]);

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
   * Sets page parameters
   *
   * @param array $parameters
   *
   * @return Page
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
   * Sets permissions required to view this page
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
   * Sets caching for page.
   *
   * @return self
   */
  protected function setCacheOptions(): self {
    $this->caching = [
      'key' => 'page',
      'tags' => ['page'],
      'context' => ['page'],
      'max-age' => -1,
    ];

    return $this;
  }

}
