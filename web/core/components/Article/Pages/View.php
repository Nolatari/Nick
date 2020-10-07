<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Article as ArticleObject;
use Nick\Entity\EntityRenderer;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class View
 *
 * @package Nick\Article\Pages
 */
class View extends Page {

  /**
   * View constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'article.view',
      'title' => $this->translate('Article'),
      'summary' => $this->translate('The view page of an article.'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.article.view',
      'context' => 'page',
      'max-age' => 300,
    ];

    if (isset($parameters[2]) && !empty($parameters[2])) {
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters[2]);
      $this->setParameter('title', $article->getTitle());
      $this->caching['key'] = $this->caching['key'] . '.' . $article->id();
      $this->caching['max-age'] = 1800;
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $content = NULL;
    if (isset($parameters[2]) && !empty($parameters[2])) {
      $id = $parameters[2];
      /** @var ArticleObject $article */
      $article = ArticleObject::load($id);
      $entityRenderer = new EntityRenderer($article);
      $content = $entityRenderer->render();

      return Nick::Renderer()
        ->setType('core.Article')
        ->setTemplate('view')
        ->render([
          'page' => [
            'id' => $this->get('id'),
            'title' => $this->get('title'),
            'summary' => $this->get('summary'),
          ],
          'article' => [
            'id' => $id ?? NULL,
            'content' => $content,
          ],
        ]);
    }

    return NULL;
  }

}
