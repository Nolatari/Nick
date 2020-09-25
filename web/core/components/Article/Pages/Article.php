<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Article as ArticleObject;
use Nick\Matter\MatterRenderer;
use Nick\Page\Page;

class Article extends Page {

  /**
   * Article constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->setParameters([
      'id' => 'article',
      'title' => $this->translate('Article'),
      'summary' => $this->translate('Creates some dummy content, users, ...'),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.' . $this->get('id'),
      'context' => 'page',
      'max-age' => 300,
    ];

    if (isset($parameters['id'])) {
      $this->caching['key'] = 'page.' . $this->get('id') . '.' . $parameters['id'];
      $this->caching['max-age'] = 0;
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters['id']);
      $this->setParameter('title', $article->getTitle());
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Article\\Pages\\Article',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);

    $articleRender = NULL;
    if (isset($parameters['id'])) {
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters['id']);
      $matterRenderer = new MatterRenderer($article);
      $articleRender = $matterRenderer->render();
      d($articleRender);
    }

    return Nick::Renderer()
      ->setType()
      ->setTemplate($this->get('id'))
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'article' => $articleRender,
      ]);
  }

}
