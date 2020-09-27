<?php

namespace Nick;

/**
 * Class Settings
 *
 * @package Nick
 */
class Settings {

  /** @var array $settings */
  protected array $settings;

  /**
   * Settings constructor.
   */
  public function __construct() {
    $this->settings = $GLOBALS['nick_settings'];
  }

  /**
   * @param string $key
   *
   * @return mixed|NULL
   */
  protected function getSetting(string $key) {
    if (StringManipulation::contains($key, '.')) {
      $keys = explode('.', $key);
      $return = $this->settings;
      foreach ($keys as $item) {
        $return = $return[$item];
      }
      return $return;
    }
    return $this->settings[$key] ?? FALSE;
  }

}