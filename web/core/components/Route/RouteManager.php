<?php

namespace Nick\Route;

use Nick\Database\Result;
use Nick\Logger;
use Nick\Translation\StringTranslation;

/**
 * Class RouteManager
 *
 * @package Nick\Route
 */
class RouteManager {
  use StringTranslation;

  /**
   * Checks whether route exists in database
   *
   * @param string $route
   *
   * @return bool
   */
  public function routeExists(string $route): bool {
    $query = \Nick::Database()
      ->select('routes')
      ->condition('route', $route)
      ->execute();
    if (!$query instanceof Result) {
      return FALSE;
    }
    $results = $query->fetchAllAssoc();
    if (count($results) > 1) {
      \Nick::Logger()->add(
        $this->translate('More than one routes with name :route found.', [':route' => $route]),
        Logger::TYPE_ERROR, 'RouteManager'
      );
      return FALSE;
    }

    return count($results) === 1;
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
