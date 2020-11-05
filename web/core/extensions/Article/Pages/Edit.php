<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Entity\Article;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Edit
 *
 * @package Nick\Article\Pages
 */
class Edit extends Page {

  /**
   * Edit constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'article.edit',
      'title' => $this->translate('Article edit'),
      'summary' => $this->translate('Edit page for an article'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.article.edit',
      'context' => 'page',
      'max-age' => 0,
    ];

    if (isset($parameters['id']) && !empty($parameters['id'])) {
      /** @var Article $article */
      $article = Article::load($parameters['id']);
      $this->setParameter('title', $this->translate('Edit :title', [':title' => $article->getTitle()]));
      $this->caching['key'] = $this->caching['key'] . '.' . $article->id();
      $this->caching['max-age'] = 0;
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $content = NULL;
    if (isset($parameters['id']) && !empty($parameters['id'])) {
      /** @var Article $article */
      $article = Article::load($parameters['id']);

      $form = \Nick::Form($article);
      $content = $form->result();
    }

    return \Nick::Renderer()
      ->setType('extension.Article')
      ->setTemplate('edit')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'article' => [
          'id' => $parameters['id'] ?? NULL,
          'content' => $content,
        ],
      ]);
  }

}
