<?php

namespace Nick;

/**
 * Class Theme
 *
 * @package Nick
 */
class Theme {

  /**
   * Returns theme from config.
   *
   * @param string $key
   *
   * @return mixed
   */
  public function getTheme($key = 'admin') {
    if ($key !== NULL) {
      return \Nick::Config()->get('theme')[$key];
    }
    return \Nick::Config()->get('theme');
  }

  /**
   * Sets theme
   *
   * @param string $theme
   *
   * @return bool
   */
  public function setTheme($theme) {
    return \Nick::Config()->set('theme', $theme);
  }

  /**
   * Returns folder where theme files/assets are located.
   *
   * @return string
   */
  public function getThemeFolder() {
    return 'themes/' . $this->getTheme() . '/';
  }

}