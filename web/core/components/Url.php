<?php

namespace Nick;

/**
 * Class URL to build dynamic urls
 */
class Url {

  /**
   * Returns URL based on route, there is no validation in this because routes are not set in stone.
   * Any validation requirements will have to be done while calling the functionality.
   *
   * @param array|string $route
   *                  Route as string or array, [0] = p | [1] = t | [2] = id
   *                  Example: extensions.install.MyExtension will return ./?p=extensions&t=install&id=MyExtension
   * @param array        $extra_params
   *                  Adds extra parameters in array, example input: ['myKey' => 'myValue', 'my-other-key' => 'my-other-value']
   *
   * @return string
   */
  public static function fromRoute($route, $extra_params = []): string {
    if (!is_array($route)) {
      $route = explode('.', $route);
    }

    $returnString = (new Url)->getBaseUrl() . '/';
    if (isset($route[0])) {
      $returnString .= '?p=' . $route[0];
    }
    if (isset($route[1])) {
      $returnString .= '&t=' . $route[1];
    }
    if (isset($route[2])) {
      $returnString .= '&id=' . $route[2];
    }

    foreach ($extra_params as $key => $value) {
      if (is_null($value)) {
        $returnString .= '&' . $key;
      } else {
        $returnString .= '&' . $key . '=' . $value;
      }
    }

    return $returnString;
  }

  /**
   * Adds a GET parameter to the current url
   *
   * @param string|array $key
   * @param string|NULL  $value
   *
   * @return string
   */
  public function addParamsToCurrentUrl($key, $value = NULL): string {
    $current_params = $_GET;
    $url = $this->getUrlWithoutParameters();

    if (is_array($key)) {
      foreach ($key as $param_key => $param_value) {
        $current_params[$param_key] = $param_value;
      }
    } else {
      $current_params[$key] = $value;
    }

    foreach ($current_params as $param_key => $param_value) {
      if ($url == $this->getUrlWithoutParameters()) {
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
   *
   * @return string
   */
  public function removeParamsFromCurrentUrl($key): string {
    $current_params = $_GET;
    $url = $this->getUrlWithoutParameters();

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
      if ($url == $this->getUrlWithoutParameters()) {
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
    return Settings::get('root.url') ?? 'http://localhost';
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
   * @return string|null
   */
  public static function getCurrentRoute(): ?string {
    return self::fromRoute([$_GET['p'] ?? NULL, $_GET['t'] ?? NULL, $_GET['id'] ?? NULL]);
  }

}