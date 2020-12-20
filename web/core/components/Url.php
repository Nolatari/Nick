<?php

namespace Nick;

use Nick\Route\RouteInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class URL to build dynamic urls
 */
class Url {

  /** @var string $url */
  protected string $url;

  /**
   * Alternative for 'http_build_query()' because this function doesn't like NULL values.
   *
   * @param array       $params
   *                      Array of parameters.
   * @param string      $separator
   *                      Separator for the parameters, default: &
   * @param string|null $prefix
   *                      Prefix for the parameters, default: ?
   *
   * @return string
   */
  public static function buildQuery(array $params, string $separator = '&', ?string $prefix = '?'): string {
    $query = $prefix ?? '';
    foreach ($params as $key => $param) {
      if ($query !== $prefix && $query !== '') {
        $query .= $separator;
      }
      if (empty($param)) {
        $query .= $key;
      } else {
        $query .= $key . '=' . $param;
      }
    }
    return $query;
  }

  /**
   * Returns URL based on route, there is no validation in this because routes are not set in stone.
   * Any validation requirements will have to be done while calling the functionality.
   *
   * @param RouteInterface $route
   *                          RouteInterface to grab URL from
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
   *                         Key of the parameter
   * @param string|null  $value
   *                         Value of the parameter
   * @param string|null  $url
   *                         Url should be clean without parameters!
   * @param array|null   $current_params
   *                         The parameters already on the URL (if a clean URL is given)
   *
   * @return string
   */
  public function addParamsToUrl($key, $value = NULL, $url = NULL, ?array $current_params = NULL): string {
    $current_params = is_array($current_params) ? $current_params : $_GET;
    $url = $url ?? $this->getUrlWithoutParameters();

    // If a URL with parameters was given, remove them
    if (StringManipulation::contains($url, '?')) {
      $url_pieces = StringManipulation::explode($url, '?');
      $url = reset($url_pieces);
    }

    if (is_array($key)) {
      foreach ($key as $param_key => $param_value) {
        $current_params[$param_key] = $param_value;
      }
    } else {
      $current_params[$key] = $value;
    }

    return $url . static::buildQuery($current_params, '&', '?');
  }

  /**
   * Adds a GET parameter to the current url
   *
   * @param string|array $key
   *                         String or array of strings that serve as the key of the parameter
   * @param string|null  $url
   *                         Url should be clean without parameters!
   * @param array|null   $current_params
   *                         The parameters already on the URL (if a clean URL is given)
   *
   * @return string
   */
  public function removeParamsFromUrl($key, $url = NULL, ?array $current_params = NULL): string {
    $current_params = is_array($current_params) ? $current_params : $_GET;
    $url = $url ?? $this->getUrlWithoutParameters();

    // If a URL with parameters was given, remove them
    if (StringManipulation::contains($url, '?')) {
      $url_pieces = StringManipulation::explode($url, '?');
      $url = reset($url_pieces);
    }

    if (is_array($key)) {
      foreach ($key as $param_key => $param_value) {
        if (isset($current_params[$param_key])) {
          unset($current_params[$param_key]);
        }
      }
    } else {
      unset($current_params[$key]);
    }

    return $url . static::buildQuery($current_params, '&', '?');
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
    return $this->getBaseUrl() . StringManipulation::replace(
        $_SERVER['REQUEST_URI'],
        Settings::get('root.web.root'),
        ''
      );
  }

  /**
   * Returns current url without parameters
   *
   * @return string
   */
  public function getUrlWithoutParameters(): string {
    return $this->getBaseUrl() . StringManipulation::replace(
        $_SERVER['SCRIPT_NAME'],
        Settings::get('root.web.root'),
        ''
      );
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

}