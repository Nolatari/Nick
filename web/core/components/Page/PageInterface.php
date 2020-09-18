<?php

namespace Nick\Page;

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
   * @param $parameter
   *
   * @return string
   */
  public function get($parameter);

  /**
   * Returns the twig render of the current page.
   * To be overwritten in the page's class.
   *
   * @return NULL|string
   */
  public function render();

  /**
   * Installs the page in the database in case it does not exist yet.
   * To be overwritten in the page's class.
   *
   * @return bool
   */
  public function install();

}