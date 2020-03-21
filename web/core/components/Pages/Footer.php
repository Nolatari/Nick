<?php

namespace Nick\Pages;

/**
 * Class Footer
 *
 * @package Nick\Pages
 */
class Footer extends Pages {

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.footer',
      'context' => 'page',
      'max-age' => 3600,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render();

    return \Nick::Renderer()
      ->setType()
      ->setTemplate('footer')
      ->render();
  }

}