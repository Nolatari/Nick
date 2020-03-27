<?php

namespace Nick;

/**
 * Class Settings
 *
 * @package Nick
 */
class Settings {

  /** @var array $settings */
  protected $settings;

  /**
   * Settings constructor.
   */
  public function __construct() {
    global $settings;
    $this->settings = $settings;
  }

  /**
   * @param string $key
   *
   * @return mixed|NULL
   */
  protected function getSetting($key) {
    return $this->settings[$key] ?? FALSE;
  }

}