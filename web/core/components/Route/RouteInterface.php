<?php

namespace Nick\Route;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface RouteInterface
 *
 * @package Nick\Route
 */
interface RouteInterface {

  /**
   * Returns either bool or route object based on route string
   *
   * @param string $route
   *
   * @return bool|self
   */
  public function load(string $route);

  /**
   * Sets single value based on key.
   *
   * @param string $key
   * @param mixed  $value
   *
   * @return self
   */
  public function setValue(string $key, $value);

  /**
   * Sets all values at once
   *
   * @param string $route
   * @param string $controller
   * @param array  $parameters
   * @param string $url
   *
   * @return self
   */
  public function setValues(string $route, string $controller, array $parameters, string $url);

  /**
   * Returns URI from Request and current Route
   *
   * @return string|string[]
   */
  public function getUri();

}