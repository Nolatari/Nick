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
   * Returns permissions required to view this page
   *
   * @return array
   */
  public function getPermissions();

  /**
   * Returns caching options for this page.
   *
   * @return array
   */
  public function getCacheOptions();

  /**
   * @return array
   */
  public function getParameters();

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
   * @return NULL|string
   */
  public function render();

}