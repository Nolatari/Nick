<?php

namespace Nick\Article;

use Nick\Article\Entity\Article;
use Nick\Database\Result;
use Nick\Event\EventListener;

/**
 * Class Events
 *
 * @package Nick\Article
 */
class Events extends EventListener {

  /**
   * {@inheritDoc}
   */
  public function preSearchRender(?array &$results, string $keyword) {
    $results['Articles'] = [];

    $articles = \Nick::Database('OR')
      ->select('entity__article')
      ->fields(NULL, ['id']);

    // Create link with authors dynamically.
    $authors = \Nick::Database()
      ->select('entity__person')
      ->fields(NULL, ['id'])
      ->condition('name', $keyword, 'LIKE')
      ->execute();
    if ($authors instanceof Result) {
      $authors_results = $authors->fetchAllAssoc();
      $ids = [];
      foreach($authors_results as $item) {
        $ids[] = $item['id'];
      }
      $articles = $articles->condition('owner', $ids, 'IN');
    }

    $articles = $articles->condition('title', $keyword, 'LIKE')
      ->condition('body', $keyword, 'LIKE')
      ->execute();

    if (!$articles instanceof Result) {
      return FALSE;
    }

    $result = $articles->fetchAllAssoc();
    foreach ($result as $item) {
      $results['Articles'][] = Article::load($item['id']);
    }
    return TRUE;
  }

}