<?php

namespace Nick\Route;

use Nick\ArrayManipulation;
use Nick\Database\Result;
use Nick\Logger;
use Nick\StringManipulation;
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
   *                  Url should not contain the base url, filter this out!
   *
   * @return bool|RouteInterface
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
      $result['parameters'] = unserialize($result['parameters']);
      if ($result['url'] === $url) {
        return \Nick::Route()->load($result['route']);
      } elseif (count($result['parameters']) > 0) {
        $exploded_route_url = ArrayManipulation::removeEmptyEntries(StringManipulation::explode($result['url'], '/'));
        $exploded_url = ArrayManipulation::removeEmptyEntries(StringManipulation::explode($url, '/'));
        // Reset numerical keys of both arrays
        $exploded_route_url = array_merge($exploded_route_url);
        $exploded_url = array_merge($exploded_url);
        $reworked_parameters = [];
        foreach ($result['parameters'] as $key => $id) {
          if (!isset($exploded_url[$id])) {
            continue;
          }

          $exploded_route_url[$id] = $exploded_url[$id];
          $reworked_parameters[$key] = $exploded_url[$id];
        }
        if ($exploded_route_url === $exploded_url) {
          return \Nick::Route()->load($result['route'])->setValue('parameters', $reworked_parameters);
        }
      }
    }

    return FALSE;
  }

}
