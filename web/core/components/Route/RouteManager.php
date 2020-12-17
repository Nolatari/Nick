<?php

namespace Nick\Route;

use Nick\ArrayManipulation;
use Nick\Database\Result;
use Nick\Logger;
use Nick\Person\Entity\Person;
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
        if (!$routing) {
          $routing = YamlReader::readComponent($extension['name'], 'routing');
        }
      }
      if (!$routing) {
        continue;
      }
      foreach ($routing as $route => $options) {
        if ($this->routeExists($route)) {
          $routeStorage = \Nick::Route()
            ->load($route)
            ->setValues($route, $options['controller'], $options['parameters'] ?? [], $options['url'], $options['rest'] ?? FALSE);
          if (!$routeStorage->save()) {
            \Nick::Logger()->add($this->translate('Something went wrong trying to save a route [:route]', [':route', $route]), Logger::TYPE_FAILURE, 'RouteManager');
          }
        } else {
          $routeStorage = \Nick::Route()
            ->setValues($route, $options['controller'], $options['parameters'] ?? [], $options['url'], $options['rest'] ?? FALSE);
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
        Logger::TYPE_ERROR,
        'RouteManager'
      );
      return FALSE;
    }

    return count($results) === 1;
  }

  /**
   * Matches given URL with route, parameters are stripped away in this method.
   *
   * @param string              $url
   *                              Url should not contain the base url, filter this out!
   *
   * @param RouteInterface|null $fallback
   *                              Fallback route to use when main route is not available
   *                              Use False to not use a fallback. Default is 'dashboard'
   *
   * @return bool|null|RouteInterface
   */
  public function routeMatch(string $url, ?RouteInterface $fallback = NULL) {
    $person = Person::getCurrentPerson();
    if ($person === 0) {
      return \Nick::Route()->load('login');
    }

    $query = \Nick::Database()
      ->select('routes')
      ->execute();
    if (!$query instanceof Result) {
      return $fallback ?? \Nick::Route()->load('error')->setValue('key', '404');
    }

    // Remove query parameters, https and http from URL
    $url = StringManipulation::preg_replace($url, '/\?(.*$)/', '');
    $url = StringManipulation::replace($url, 'https://', '');
    $url = StringManipulation::replace($url, 'http://', '');

    $results = $query->fetchAllAssoc();
    foreach ($results as $result) {
      $result['parameters'] = unserialize($result['parameters']);
      if ($result['url'] === $url) {
        return \Nick::Route()->load($result['route']);
      }
    }

    foreach ($results as $result) {
      $result['parameters'] = unserialize($result['parameters']);
      if (count($result['parameters']) > 0) {
        $exploded_route_url = ArrayManipulation::removeEmptyEntries(StringManipulation::explode($result['url'], '/'), TRUE, TRUE);
        $exploded_url = ArrayManipulation::removeEmptyEntries(StringManipulation::explode($url, '/'), TRUE, TRUE);
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

    return $fallback ?? \Nick::Route()->load('error')->setValue('key', '404');
  }

}
