<?php

namespace Nick\Person\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Logout
 *
 * @package Nick\Person
 */
class Logout extends Page {

  /**
   * Login constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'logout',
      'title' => $this->translate('Logout'),
      'summary' => $this->translate('Logout from :sitename.', [
        ':sitename' => \Nick::Config()->get('site.name'),
      ]),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'page.logout',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();

    \Nick::Person()::logout();

    $dashboard = \Nick::Route()->load('dashboard');
    $redirect = new RedirectResponse($dashboard->getUrl());
    $redirect->send();
  }

}
