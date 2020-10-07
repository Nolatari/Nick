<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Article as ArticleObject;
use Nick\Form\Form;
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
      'max-age' => 300,
    ];

    if (isset($parameters[2]) && !empty($parameters[2])) {
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters[2]);
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
    if (isset($parameters[2]) && !empty($parameters[2])) {
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters[2]);

      $form = new Form($article);
      $content = $form->result();
    }

    return Nick::Renderer()
      ->setType('core.Article')
      ->setTemplate('edit')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'article' => [
          'id' => $parameters[2] ?? NULL,
          'content' => $content,
        ],
      ]);
  }

}
