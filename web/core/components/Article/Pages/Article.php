<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Article as ArticleObject;
use Nick\Form\Form;
use Nick\Matter\MatterRenderer;
use Nick\Page\Page;

class Article extends Page {

  /**
   * Article constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'article',
      'title' => $this->translate('Article'),
      'summary' => $this->translate('Creates some dummy content, users, ...'),
    ]);
    parent::__construct();
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
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters['id']);
      $this->setParameter('title', $article->getTitle());
      $this->caching['key'] = 'page.' . $this->get('id') . '.' . $article->id();
      $this->caching['max-age'] = 1800;
      if (isset($parameters['t']) && !empty($parameters['t'])) {
        $this->caching['key'] = 'page.' . $this->get('id') . '.' . $parameters['t'] . '.' . $article->id();
        $this->caching['max-age'] = 0;
      }
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

    $content = NULL;
    if (isset($parameters['id'])) {
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters['id']);
      $matterRenderer = new MatterRenderer($article);
      $content = $matterRenderer->render();

      if (isset($parameters['t']) && $parameters['t'] == 'edit') {
        $form = new Form($article);
        $content = $form->result();
      }
    }

    return Nick::Renderer()
      ->setType('core.Article')
      ->setTemplate($this->get('id'))
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'article' => [
          'id' => $parameters['id'] ?? NULL,
          'type' => $parameters['t'] ?? 'view',
          'content' => $content,
        ],
      ]);
  }

}
