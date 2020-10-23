<?php

namespace Nick;

/**
 * Class Settings
 *
 * @package Nick
 */
class Settings {

  /** @var array $settings */
  public static array $settings = [];

  /**
   * Sets setting array
   *
   * @param array|null $settings
   */
  public static function setSettings(?array &$settings = NULL) {
    static::$settings = $settings ?? $GLOBALS['nick_settings'];
  }

  /**
   * @param string $key
   *                  Key can be for example: category.item for recursive array keys
   *
   * @return false|mixed
   */
  public static function get(string $key) {
    if (StringManipulation::contains($key, '.')) {
      $keys = StringManipulation::explode($key, '.');
      $return = static::$settings;
      foreach ($keys as $item) {
        $return = &$return[$item];
      }
      return $return;
    }
    return static::$settings[$key] ?? FALSE;
  }

  /**
   * @return array
   */
  public static function getAll(): array {
    return static::$settings;
  }

}