<?php

namespace Nick\Route;

use Nick\ArrayManipulation;
use Nick\Database\Result;
use Nick\Logger;
use Nick\StringManipulation;
use Nick\Translation\StringTranslation;
use Nick\YamlReader;

/**
 * Class RouteManager
 *
 * @package Nick\Route
 */
class RouteManager {
  use StringTranslation;

  /**
   * Installs routes from extension.routing.yml files
   */
  public function installRoutes() {
    $extensions = \Nick::ExtensionManager()::getInstalledExtensions();
    $extensions[] = [
      'type' => 'core',
      'name' => 'core',
    ];
    foreach ($extensions as $extension) {
      if ($extension['name'] === 'core') {
        $routing = YamlReader::readCore('routing');
      } else {
        $routing = YamlReader::readExtension($extension['name'], 'routing');
      }
      if (!$routing) {
        continue;
      }
      foreach ($routing as $route => $options) {
        if ($this->routeExists($route)) {
          $routeStorage = \Nick::Route()
            ->load($route)
            ->setValues($route, $options['controller'], $options['parameters'] ?? [], $options['url']);
          if (!$routeStorage->save()) {
            \Nick::Logger()->add($this->translate('Something went wrong trying to save a route [:route]', [':route', $route]), Logger::TYPE_FAILURE, 'RouteManager');
          }
        } else {
          $routeStorage = \Nick::Route()
            ->setValues($route, $options['controller'], $options['parameters'] ?? [], $options['url']);
          if (!$routeStorage->save()) {
            \Nick::Logger()->add($this->translate('Something went wrong trying to add a route [:route]', [':route', $route]), Logger::TYPE_FAILURE, 'RouteManager');
          }
        }
      }
    }
  }

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
   * @param string $fallback
   *                  Fallback route to use when main route is not available
   *                  Use False to not use a fallback. Default is 'dashboard'
   *
   * @return bool|RouteInterface
   */
  public function routeMatch(string $url, $fallback = 'dashboard') {
    $query = \Nick::Database()
      ->select('routes')
      ->execute();
    if (!$query instanceof Result) {
      if (!$fallback) {
        return FALSE;
      }
      return $this->routeMatch($fallback);
    }

    // Remove query parameters from URL
    $url = preg_replace('/\?(.*$)/', '', $url);

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
          $route = \Nick::Route()->load($result['route']);
          foreach ($reworked_parameters as $key => $value) {
            $route = $route->setValue($key, $value);
          }
          return $route;
        }
      }
    }

    if (!$fallback) {
      return FALSE;
    }
    if($this->routeExists($fallback)) {
      return \Nick::Route()->load($fallback);
    }
    return FALSE;
  }

}
