<?php

namespace Nick\Pages;

/**
 * Class Dashboard
 *
 * @package Nick\Pages
 */
class Dashboard extends Pages {

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.dashboard',
      'context' => 'page',
      'max-age' => 1800,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('dashboard')
      ->render();
  }

}