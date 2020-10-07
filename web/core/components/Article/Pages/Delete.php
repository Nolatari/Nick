<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Article as ArticleObject;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\StringManipulation;
use Nick\Url;

/**
 * Class Delete
 *
 * @package Nick\Article\Pages
 */
class Delete extends Page {

  /**
   * Delete constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'article.delete',
      'title' => $this->translate('Article Delete'),
      'summary' => $this->translate('Delete page for an article'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.article.delete',
      'context' => 'page',
      'max-age' => 300,
    ];

    if (isset($parameters[2]) && !empty($parameters[2])) {
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters[2]);
      $this->setParameter('title', $this->translate('Delete :title', [':title' => $article->getTitle()]));
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

      if (isset($parameters[4]) && StringManipulation::contains($parameters[4], 'confirm')) {
        $article->delete();
        header('Location: ' . Url::fromRoute(Nick::Route()->load('article.overview')));
      }

      $content = 'Are you sure you wish to delete this ' . $article->getTitle() . '? <br />';
      $content .= '<a class="btn btn-primary" href="' . Url::fromRoute(\Nick::Route()->load('article.delete')->setValue('id', $parameters[2])->setValue('confirm', NULL)) . '">Yes, I\'m sure</a> ';
      $content .= '<a class="btn btn-danger" href="' . Url::fromRoute(\Nick::Route()->load('article.view')->setValue('id', $parameters[2])) . '">No, take me back</a>';
    }

    return Nick::Renderer()
      ->setType('core.Article')
      ->setTemplate('delete')
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
