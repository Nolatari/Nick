<?php

namespace Nick\Page;

use Nick\Route\RouteInterface;

/**
 * Interface PageInterface
 *
 * @package Nick\Page
 */
interface PageInterface {

  /**
   * Returns caching options for this page.
   *
   * @return array
   */
  public function getCacheOptions();

  /**
   * Returns page parameter
   *
   * @param string $parameter
   *
   * @return string
   */
  public function get(string $parameter);

  /**
   * Returns the twig render of the current page.
   * To be overwritten in the page's class.
   *
   * @param array          $parameters
   * @param RouteInterface $route
   *
   * @return NULL|string
   */
  public function render(array &$parameters, RouteInterface $route);

  /**
   * Installs the page in the database in case it does not exist yet.
   * To be overwritten in the page's class.
   *
   * @return bool
   */
  public function install();

}