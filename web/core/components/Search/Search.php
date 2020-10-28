<?php

namespace Nick\Search;

use Nick\Event\Event;

/**
 * Class Search
 *
 * @package Nick\Search
 */
class Search {

  /** @var string $keyword */
  protected string $keyword;

  /**
   * Search constructor.
   *
   * @param string $keyword
   */
  public function __construct(string $keyword) {
    $this->setKeyword($keyword);
  }

  /**
   * Returns the set keyword
   *
   * @return string
   */
  public function getKeyword(): string {
    return $this->keyword;
  }

  /**
   * Returns the set keyword
   *
   * @param string $keyword
   *
   * @return Search
   */
  public function setKeyword(string $keyword): self {
    $this->keyword = $keyword;
    return $this;
  }

  /**
   * Get search results from all entities
   *
   * @return array
   */
  public function getSearchResults(): array {
    $results = [];

    \Nick::Event('preSearchRender')
      ->fire($results, [$this->getKeyword()]);

    return $results;
  }

}