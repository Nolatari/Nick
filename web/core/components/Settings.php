<?php

namespace Nick;

/**
 * Class Settings
 *
 * @package Nick
 */
class Settings {

  /**
   * @param string $key
   *
   * @return false|mixed
   */
  public static function get(string $key) {
    $settings = $GLOBALS['nick_settings'];
    if (StringManipulation::contains($key, '.')) {
      $keys = explode('.', $key);
      $return = $settings;
      foreach ($keys as $item) {
        $return = $return[$item];
      }
      return $return;
    }
    return $settings[$key] ?? FALSE;
  }

  /**
   * @return array
   */
  public static function getAll(): array {
    return $GLOBALS['nick_settings'];
  }

}