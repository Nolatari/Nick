<?php

namespace Nick;

/**
 * Class URL to build dynamic urls
 */
class Url extends Settings {

  /**
   * Adds a GET parameter to the current url
   *
   * @param string|array $key
   * @param string|NULL $value
   *
   * @return string
   */
  public function addParamsToCurrentUrl($key, $value = NULL) {
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
      if ($url == $this->getUrlWithoutParameters()) { $url .= '?'; }
      else { $url .= '&'; }
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
  public function removeParamsFromCurrentUrl($key) {
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
      if ($url == $this->getUrlWithoutParameters()) { $url .= '?'; }
      else { $url .= '&'; }
      $url .= $param_key . '=' . $param_value;
    }

    return $url;
  }

  /**
   * Returns the site's base url (no filename, no parameters)
   */
  public function getBaseUrl() {
    return $this->getSetting('uri') ?? 'http://localhost';
  }

  /**
   * Returns current url with parameters
   *
   * @result string
   */
  public function getUrl() {
    return $this->getBaseUrl() . $_SERVER['REQUEST_URI'];
  }

  /**
   * Returns current url without parameters
   *
   * @result string
   */
  public function getUrlWithoutParameters() {
    return $this->getBaseUrl() . $_SERVER['SCRIPT_NAME'];
  }

  /**
   * Returns the current path with parameters
   */
  public static function getCurrentPath() {
    return $_SERVER['REQUEST_URI'];
  }

}