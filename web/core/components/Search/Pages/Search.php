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
  public function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'page.' . $this->get('id'),
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    $query = $_GET['q'] ?? NULL;
    $results = \Nick::Search($query)->getSearchResults();

    foreach ($results as $category => &$items) {
      if (!is_array($items)) {
        continue;
      }

      foreach ($items as $key => &$item) {
        $item = \Nick::EntityRenderer($item)->render([], 'search-result');
      }
    }

    return \Nick::Renderer()
      ->setType('core.Search')
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