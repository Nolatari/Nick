<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Entity\Article;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Add
 *
 * @package Nick\Article\Pages
 */
class Add extends Page {

  /**
   * Edit constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'article.add',
      'title' => $this->translate('Article add'),
      'summary' => $this->translate('Add page for an article'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.article.add',
      'context' => 'page',
      'max-age' => 300,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $article = new Article();
    $form = \Nick::Form($article);
    $content = $form->result();

    return \Nick::Renderer()
      ->setType('extension.Article')
      ->setTemplate('add')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'article' => [
          'content' => $content,
        ],
      ]);
  }

}
