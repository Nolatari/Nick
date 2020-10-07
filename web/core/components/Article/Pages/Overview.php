<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Article as ArticleObject;
use Nick\Form\Form;
use Nick\Manifest\Manifest;
use Nick\Manifest\ManifestRenderer;
use Nick\Entity\EntityRenderer;
use Nick\Page\Page;
use Nick\Url;

/**
 * Class Overview
 *
 * @package Nick\Article\Pages
 */
class Overview extends Page {

  /**
   * Overview constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'article.overview',
      'title' => $this->translate('Article overview'),
      'summary' => $this->translate('List of existing articles.'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.article.overview',
      'context' => 'page',
      'max-age' => 300,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Article\\Pages\\Overview',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render(&$parameters = []) {
    parent::render($parameters);

    $content = NULL;
    $manifest = Nick::Manifest('article')->fields([
      'id', 'title', 'status', 'owner'
    ]);
    $manifestRenderer = new ManifestRenderer($manifest);
    $content = $manifestRenderer
      ->setViewMode($parameters['viewmode'] ?? 'table')
      ->hideField('id')
      ->noLink('owner')
      ->addActionLinks('article')
      ->render(TRUE);

    return Nick::Renderer()
      ->setType('core.Article')
      ->setTemplate('overview')
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
