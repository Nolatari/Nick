<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Overview
 *
 * @package Nick\Article\Pages
 */
class Overview extends Page {

  /**
   * Overview constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'article.overview',
      'title' => $this->translate('Article overview'),
      'summary' => $this->translate('List of existing articles.'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'page.article.overview',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();

    $manifest = \Nick::Manifest('article')->fields([
      'id', 'title', 'status', 'owner'
    ]);
    $content = \Nick::ManifestRenderer($manifest)
      ->setViewMode($parameters['viewmode'] ?? 'table')
      ->hideField('id')
      ->noLink('owner')
      ->addActionLinks('article')
      ->render(TRUE);

    return \Nick::Renderer()
      ->setType('extension.Article')
      ->setTemplate('overview')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'articles' => [
          'content' => $content,
        ],
      ]);
  }

}
