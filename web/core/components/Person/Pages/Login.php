<?php

namespace Nick\Person\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Person\Entity\Person;
use Nick\Person\Entity\PersonInterface;
use Nick\Route\RouteInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Login
 *
 * @package Nick\Person
 */
class Login extends Page {

  /**
   * Login constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'login',
      'title' => $this->translate('Login'),
      'summary' => $this->translate('Login to :sitename.', [
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
      'key' => 'page.login',
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

    $request = \Nick::Request();
    if ($request->request->has('person-name') && $request->request->has('person-password')) {
      $person = [];
      $person['name'] = $request->request->get('person-name');
      $person['password'] = $request->request->get('person-password');

      /** @var Person $storage */
      $storage = \Nick::EntityManager()->loadByProperties([
        'type' => 'person',
        'name' => $person['name'],
      ]);
      if ($storage instanceof PersonInterface) {
        if ($storage->checkPassword($person['password'])) {
          \Nick::Person()::changeTo($storage->id());
          $message = [
            'type' => 'success',
            'message' => $this->translate('Login successful.'),
          ];

          $dashboard = \Nick::Route()->load('dashboard');
          $redirect = new RedirectResponse($dashboard->getUrl());
          $redirect->send();
        } else {
          $message = [
            'type' => 'danger',
            'message' => $this->translate('Login failed.'),
          ];
        }
      } else {
        $message = [
          'type' => 'danger',
          'message' => $this->translate('Login failed.'),
        ];
      }
    }

    return \Nick::Renderer()
      ->setType()
      ->setTemplate('login')
      ->render([
        'message' => $message ?? [],
      ]);
  }

}