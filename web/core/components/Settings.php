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

  /** @var string $subsite */
  public static string $subsite = '';

  /**
   * Sets setting array
   *
   * @param array|null $settings
   */
  public static function setSettings(?array &$settings = NULL) {
    $all_settings = &$GLOBALS['nick_settings'];
    if (!is_array($settings)) {
      foreach ($all_settings as $subsite => $values) {
        if (StringManipulation::contains($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $values['url'])) {
          static::$subsite = $subsite;
        }
      }
    }
    static::$settings = $settings ?? $all_settings[static::$subsite];
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