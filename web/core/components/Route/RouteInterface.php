<?php

namespace Nick\Route;

use Nick\Page\PageInterface;

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
   * Keys 'parameters', 'controller', 'route' and 'url' are taken by the system
   * Other keys will be added to the $values property.
   *
   * @param string $key
   *                  Key of the value to be set.
   * @param mixed  $value
   *                  The value to be set.
   *
   * @return self
   */
  public function setValue(string $key, $value);

  /**
   * Sets all values at once
   *
   * @param string $route
   *                  Route as string, e.g: person.edit
   * @param string $controller
   *                  Controller class as string, e.g:
   *                  \Nick\Person\Pages\Edit
   * @param array  $parameters
   *                  Array of reusable parameters (e.g: ['id' => 1])
   *                  where the integer 1 represents the place of the parameter
   *                  in the url. For example: /person/{id}/edit
   * @param string $url
   *                  The URL the route should link to.
   *                  Use reusable URLs here, e.g: /person/{id}/edit
   * @param bool   $rest
   *                  Whether this is a call to the rest API or not (handles bootstrap differently)
   *
   * @return self
   */
  public function setValues(string $route, string $controller, array $parameters, string $url, bool $rest = FALSE);

  /**
   * Returns URI from Request and current Route
   *
   * @return string|string[]
   */
  public function getUri();

  /**
   * Renders route object (if it complies with PageInterface).
   *
   * @return mixed|null
   */
  public function render();

  /**
   * Returns the page/element object
   *
   * @return null|PageInterface
   */
  public function getObject();

}