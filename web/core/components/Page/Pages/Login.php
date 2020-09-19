<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;

/**
 * Class Error
 *
 * @package Nick\Login
 */
class Login extends Page {

  /**
   * Login constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->setParameters([
      'title' => $this->translate('Login'),
      'summary' => $this->translate('Login to :sitename.', [
        ':sitename' => Nick::Config()->get('site.name'),
      ]),
    ]);
  }

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
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Page\\Pages\\Login',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);
    return Nick::Renderer()
      ->setType()
      ->setTemplate('login')
      ->render();
  }

}