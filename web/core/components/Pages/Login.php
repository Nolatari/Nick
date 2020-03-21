<?php

namespace Nick\Pages;

/**
 * Class Error
 *
 * @package Nick\Login
 */
class Login extends Pages {

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.login',
      'context' => 'page',
      'max-age' => 3600,
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
      ->setTemplate('login')
      ->render();
  }

}