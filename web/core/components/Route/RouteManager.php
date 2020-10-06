<?php

namespace Nick\Route;

use Nick\Database\Result;

/**
 * Class RouteManager
 *
 * @package Nick\Route
 */
class RouteManager {

  /**
   * Checks whether route exists in database
   *
   * @param string $route
   *
   * @return bool
   */
  public function routeExists(string $route): bool {
    return TRUE;
  }

  /**
   * @param string $url
   *
   * @return bool
   */
  public function routeMatch(string $url) {
    $query = \Nick::Database()
      ->select('routes')
      ->execute();

    if (!$query instanceof Result) {
      return FALSE;
    }
    $results = $query->fetchAllAssoc();
    foreach ($results as $result) {
      d($result);
    }
  }

}
