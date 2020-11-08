<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Entity\Article;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\StringManipulation;
use Nick\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

    if (isset($parameters[1]) && !empty($parameters[1])) {
      /** @var Article $article */
      $article = Article::load($parameters[1]);
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
    if (isset($parameters['id']) && !empty($parameters['id'])) {
      /** @var Article $article */
      $article = Article::load($parameters['id']);

      if (isset($parameters[3]) && StringManipulation::contains($parameters[3], 'confirm')) {
        $article->delete();
        $response = new RedirectResponse(Url::fromRoute(\Nick::Route()->load('article.overview')));
        $response->send();
      }

      $content = 'Are you sure you wish to delete this ' . $article->getTitle() . '? <br />';
      $content .= '<a class="btn btn-primary" href="' . Url::fromRoute(\Nick::Route()->load('article.delete')->setValue('id', $parameters['id'])->setValue('confirm', NULL)) . '">Yes, I\'m sure</a> ';
      $content .= '<a class="btn btn-danger" href="' . Url::fromRoute(\Nick::Route()->load('article.view')->setValue('id', $parameters['id'])) . '">No, take me back</a>';
    }

    return \Nick::Renderer()
      ->setType('extension.Article')
      ->setTemplate('delete')
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
