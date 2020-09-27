<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;

/**
 * Class Footer
 *
 * @package Nick\Page
 */
class Footer extends Page {

  /**
   * Footer constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'footer',
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
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
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Page\\Pages\\Footer',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render(&$parameters = []) {
    return Nick::Renderer()
      ->setType()
      ->setTemplate('footer')
      ->render();
  }

}