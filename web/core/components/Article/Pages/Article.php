<?php

namespace Nick\Article\Pages;

use Nick;
use Nick\Article\Article as ArticleObject;
use Nick\Form\Form;
use Nick\Manifest\Manifest;
use Nick\Manifest\ManifestRenderer;
use Nick\Matter\MatterRenderer;
use Nick\Page\Page;
use Nick\Url;

/**
 * Class Article
 *
 * @package Nick\Article\Pages
 */
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

    if (isset($parameters['id']) && !empty($parameters['id'])) {
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters['id']);
      $this->setParameter('title', $article->getTitle());
      $this->caching['key'] = 'page.' . $this->get('id') . '.view.' . $article->id();
      $this->caching['max-age'] = 1800;
      if (isset($parameters['t']) && !empty($parameters['t'])) {
        $this->caching['key'] = 'page.' . $this->get('id') . '.' . $parameters['t'] . '.' . $article->id();
        $this->caching['max-age'] = 0;
      }
    } elseif (isset($parameters['t']) && $parameters['t'] == 'overview') {
      $this->caching['key'] = 'page.' . $this->get('id') . '.' . $parameters['t'];
      $this->caching['max-age'] = 0;
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
  public function render(&$parameters = []) {
    parent::render($parameters);

    $content = NULL;
    if (isset($parameters['id']) && !empty($parameters['id'])) {
      /** @var ArticleObject $article */
      $article = ArticleObject::load($parameters['id']);
      $matterRenderer = new MatterRenderer($article);
      $content = $matterRenderer->render();

      if (isset($parameters['t'])) {
        switch ($parameters['t']) {
          case 'edit':
            $form = new Form($article);
            $content = $form->result();
            break;
          case 'delete':
            $content = 'Are you sure you wish to delete this article? <br />';
            $content .= '<a class="btn btn-primary" href="' . Url::fromRoute(['article', 'delete', $parameters['id']], ['confirm' => NULL]) . '">Yes, I\'m sure</a> ';
            $content .= '<a class="btn btn-danger" href="' . Url::fromRoute(['article', 'view', $parameters['id']]) . '">No, take me back</a>';
            break;
        }
      }
    } elseif (isset($parameters['t'])) {
      switch ($parameters['t']) {
        case 'overview':
          $manifest = Nick::Manifest('article')->fields([
            'id', 'title', 'owner', 'status'
          ]);
          $manifestRenderer = new ManifestRenderer($manifest);
          $content = $manifestRenderer
            ->setViewMode('table')
            ->hideField('id')
            ->hideField('status')
            ->noLink('owner')
            ->addActionLinks('article')
            ->render(TRUE);
          break;
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
