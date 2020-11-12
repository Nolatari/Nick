<?php

namespace Nick\Person;

use Nick\Database\Result;
use Nick\Event\EventListener;
use Nick\Person\Entity\Person;

/**
 * Class Events
 *
 * @package Nick\Person
 */
class Events extends EventListener {

  /**
   * {@inheritDoc}
   */
  public function preSearchRender(?array &$results, string $keyword) {
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