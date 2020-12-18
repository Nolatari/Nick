<?php

namespace Nick\Page;

use Nick\Route\RouteInterface;

/**
 * Interface ElementInterface
 *
 * @package Nick\Page
 */
interface ElementInterface {

  /**
   * Returns permissions required to view this element
   *
   * @return array
   */
  public function getPermissions();

  /**
   * Returns caching options for this element.
   *
   * @return array
   */
  public function getCacheOptions();

  /**
   * Returns element parameter
   *
   * @param string $parameter
   *
   * @return string
   */
  public function get(string $parameter);

  /**
   * Returns the twig render of the current element.
   * To be overwritten in the element's class.
   *
   * @param array          $parameters
   * @param RouteInterface $route
   *
   * @return NULL|string
   */
  public function render(array &$parameters, RouteInterface $route);

}
