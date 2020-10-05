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
    $this->setParameters([
      'title' => $this->translate('Login'),
      'summary' => $this->translate('Login to :sitename.', [
        ':sitename' => Nick::Config()->get('site.name'),
      ]),
    ]);
    parent::__construct();
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
  public function render(&$parameters = []) {
    parent::render($parameters);
    return Nick::Renderer()
      ->setType()
      ->setTemplate('login')
      ->render();
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.login',
      'context' => 'page',
      'max-age' => 3600,
    ];

    return $this;
  }

}