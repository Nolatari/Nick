<?php

namespace Nick\Search\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Search
 *
 * @package Nick\Page
 */
class Search extends Page {

  /**
   * Search constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'search',
      'title' => $this->translate('Search'),
      'summary' => $this->translate('Search results for :keyword', [':keyword' => 'undefined']),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.' . $this->get('id'),
      'context' => 'page',
      'max-age' => 0,
    ];

    if ($parameters['q']) {
      $this->caching = [
        'key' => 'page.' . $this->get('id') . '.q.' . $parameters['q'],
        'context' => 'page',
        'max-age' => 0,
      ];
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
      'controller' => '\\Nick\\Search\\Pages\\Search',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $results = Nick::Search($parameters['q'])->getSearchResults();

    foreach ($results as $category => &$items) {
      if (!is_array($items)) {
        continue;
      }

      foreach ($items as $key => &$item) {
        $item = Nick::EntityRenderer($item)->render();
      }
    }

    return Nick::Renderer()
      ->setType()
      ->setTemplate('search')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'results' => $results,
      ]);
  }

}