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
    d($GLOBALS['nick_settings']);
    $this->settings = $GLOBALS['nick_settings'];
  }

  /**
   * @param string $key
   *
   * @return mixed|NULL
   */
  protected function getSetting(string $key) {
    return $this->settings[$key] ?? FALSE;
  }

}