<?php

namespace Nick\Person;

use Nick\Database\Result;

/**
 * Class Events
 *
 * @package Nick\Person
 */
class Events {

  /**
   * preSearchRender event listener
   *
   * @param array  $results
   * @param string $keyword
   *
   * @return bool
   */
  public function preSearchRender(array &$results, string $keyword) {
    $results['People'] = [];

    $query = \Nick::Database()
      ->select('entity__person')
      ->fields(NULL, ['id'])
      ->condition('name', $keyword, 'LIKE')
      ->execute();

    if (!$query instanceof Result) {
      return FALSE;
    }
    $result = $query->fetchAllAssoc();
    foreach ($result as $item) {
      $results['People'][] = Person::load($item['id']);
    }
    return TRUE;
  }

}