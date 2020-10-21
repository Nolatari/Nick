<?php

namespace Nick;

use Nick\Route\RouteInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class URL to build dynamic urls
 */
class Url {

  /**
   * Returns URL based on route, there is no validation in this because routes are not set in stone.
   * Any validation requirements will have to be done while calling the functionality.
   *
   * @param RouteInterface $route
   *                  RouteInterface to grab URL from
   *
   * @return string
   */
  public static function fromRoute(RouteInterface $route): string {
    return Settings::get('root.web.url') . $route->getUri();
  }

  /**
   * Adds a GET parameter to the current url
   *
   * @param string|array $key
   * @param string|null  $value
   * @param string|null  $url
   * @param array|null   $current_params
   *
   * @return string
   */
  public function addParamsToUrl($key, $value = NULL, $url = NULL, ?array $current_params = NULL): string {
    $current_params = is_array($current_params) ? $current_params : $_GET;
    $url = $url ?? $this->getUrlWithoutParameters();
    $baseUrl = $url ?? $this->getUrlWithoutParameters();

    if (is_array($key)) {
      foreach ($key as $param_key => $param_value) {
        $current_params[$param_key] = $param_value;
      }
    } else {
      $current_params[$key] = $value;
    }

    foreach ($current_params as $param_key => $param_value) {
      if ($url === $baseUrl) {
        $url .= '?';
      } else {
        $url .= '&';
      }
      $url .= $param_key . '=' . $param_value;
    }

    return $url;
  }

  /**
   * Adds a GET parameter to the current url
   *
   * @param string|array $key
   * @param null         $url
   * @param array|null   $current_params
   *
   * @return string
   */
  public function removeParamsFromUrl($key, $url = NULL, ?array $current_params = NULL): string {
    $current_params = is_array($current_params) ? $current_params : $_GET;
    $url = $url ?? $this->getUrlWithoutParameters();
    $baseUrl = $url ?? $this->getUrlWithoutParameters();

    if (is_array($key)) {
      foreach ($key as $param_key => $param_value) {
        if (isset($current_params[$param_key])) {
          unset($current_params[$param_key]);
        }
      }
    } else {
      unset($current_params[$key]);
    }

    foreach ($current_params as $param_key => $param_value) {
      if ($url === $baseUrl) {
        $url .= '?';
      } else {
        $url .= '&';
      }
      $url .= $param_key . '=' . $param_value;
    }

    return $url;
  }

  /**
   * Returns the site's base url (no filename, no parameters)
   */
  public function getBaseUrl(): string {
    return Settings::get('root.web.url') ?? 'http://localhost';
  }

  /**
   * Returns current url with parameters
   *
   * @return string
   */
  public function getUrl(): string {
    return $this->getBaseUrl() . $_SERVER['REQUEST_URI'];
  }

  /**
   * Returns current url without parameters
   *
   * @return string
   */
  public function getUrlWithoutParameters(): string {
    return $this->getBaseUrl() . $_SERVER['SCRIPT_NAME'];
  }

  /**
   * Returns the current path with parameters
   *
   * @return string
   */
  public static function getCurrentPath(): string {
    return $_SERVER['REQUEST_URI'];
  }

  /**
   * Returns URL parameters in array form (including clean URL parameters!)
   *
   * @return array
   */
  public static function getParameters() {
    $request = Request::createFromGlobals();
    $uri = StringManipulation::replace($request->getUri(), Settings::get('root.web.url'), '');
    $parameters = ArrayManipulation::removeEmptyEntries(StringManipulation::explode($uri, '/'));
    foreach ($parameters as $key => $value) {
      if (StringManipulation::contains($value, '?')) {
        $elements = StringManipulation::explode($value, '?');
        $parameters[$key] = $elements[0];
        $parameters[] = StringManipulation::replace($value, $elements[0] . '?', '');
      }
    }
    $parameters = array_merge($parameters);
    return $parameters;
  }

  /**
   * Returns URL parameters in array form (including clean URL parameters!)
   * But also replaces the given integer keys with the keys defined by the route parameters.
   *
   * @param RouteInterface $route
   *
   * @return array
   */
  public static function getRefactoredParameters(RouteInterface $route) {
    $parameters = static::getParameters();
    foreach ($parameters as $key => $parameter) {
      foreach ($route->getParameters() as $k => $id) {
        if ($key === $id && $route->getValue($k) === NULL) {
          $parameters = ArrayManipulation::moveKey($parameters, $id, $k);
        } elseif ($route->getValue($k) !== NULL) {
          $parameters[$k] = $route->getValue($k);
        }
      }
    }
    return $parameters;
  }

  /**
   * @return RouteInterface|bool
   */
  public static function getCurrentRoute() {
    $request = Request::createFromGlobals();
    $uri = StringManipulation::replace($request->getUri(), Settings::get('root.web.url'), '');
    return \Nick::RouteManager()->routeMatch(StringManipulation::replace($uri, Settings::get('root.web.url')));
  }

}