<?php

namespace Nick\DummyContent;

use Nick;
use Nick\Article\Article;
use Nick\ExtensionManager\ExtensionManager;
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
    if (!ExtensionManager::extensionInstalled('Person')) {
      return 'Person extension is not installed.';
    }
    $person = new Person([
      'status' => Matter::PUBLISHED,
      'owner' => 1,
      'name' => 'Admin',
      'password' => '$2y$10$vw4KCNOucAF4bjcTsrnIZO7/KAtWBHj9bMKGy4U4riVvOyZ9dLi4e',
    ]);
    $person->save();
    return 'Created new person \'Admin\'.';
  }

  public function createArticle() {
    if (!ExtensionManager::extensionInstalled('Article')) {
      return 'Article extension is not installed.';
    }
    $article = new Article([
      'status' => Matter::PUBLISHED,
      'owner' => 1,
      'title' => 'My first article',
      'body' => 'This is my first article!\n\nLorem ipsum, and what not :-)',
    ]);
    $article->save();
    return 'Created new article \'My first article\'';
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.' . $this->get('id'),
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(&$parameters = []) {
    parent::render($parameters);

    $message = 'Do you wish to create dummy content?';
    $confirm = FALSE;
    if (isset($parameters['confirm'])) {
      $confirm = TRUE;
      if (isset($parameters['t']) && $parameters['t'] == 'person') {
        $message = $this->createPerson();
      } elseif (isset($parameters['t']) && $parameters['t'] == 'article') {
        $message = $this->createArticle();
      }
    }

    return Nick::Renderer()
      ->setType('extension.DummyContent')
      ->setTemplate($this->get('id'))
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'message' => $message,
        'confirm' => $confirm,
      ]);
  }

}