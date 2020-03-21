<?php

namespace Nick\Pages;

interface PagesInterface {

  /**
   * Returns caching options for this page.
   *
   * @return array
   */
  public function getCacheOptions();

  /**
   * Returns the twig render of the current page.
   * To be overwritten in the page's class.
   *
   * @return NULL|string
   */
  public function render();

}