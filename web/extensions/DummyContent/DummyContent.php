<?php

namespace Nick\DummyContent;

use Nick;
use Nick\Matter\Matter;
use Nick\Page\Page;
use Nick\Person\Person;

class DummyContent extends Page {

  /**
   * DummyContent constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->setParameters([
      'id' => 'dummycontent',
      'title' => $this->translate('DummyContent'),
      'summary' => $this->translate('Creates some dummy content, users, ...'),
    ]);
  }

  /**
   *
   */
  public function createPerson() {
    $person = new Person([
      'status' => Matter::PUBLISHED,
      'owner' => 1,
      'name' => 'Admin',
      'password' => '$2y$10$vw4KCNOucAF4bjcTsrnIZO7/KAtWBHj9bMKGy4U4riVvOyZ9dLi4e',
    ]);
    $person->save();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions() {
    $this->caching = [
      'key' => 'page.dummycontent',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);

    if (isset($parameters['person'])) {
      $this->createPerson();
    }

    return Nick::Renderer()
      ->setType('extension.DummyContent')
      ->setTemplate('dummycontent')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
      ]);
  }

}