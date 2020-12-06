<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

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
        ':sitename' => \Nick::Config()->get('site.name'),
      ]),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []): self {
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
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('login')
      ->render();
  }

}