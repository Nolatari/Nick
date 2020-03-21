<?php

namespace Nick\Pages;

/**
 * Class Header
 *
 * @package Nick\Pages
 */
class Header extends Pages {

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.header',
      'context' => 'page',
      'max-age' => 900,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('header')
      ->render($parameters ?? NULL);
  }

}