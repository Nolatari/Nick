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
   *          The type of theme (admin/front).
   *
   * @return mixed
   */
  public function getTheme($key = 'admin') {
    if ($key !== NULL) {
      return \Nick::Config()->get('theme.' . $key);
    }
    return \Nick::Config()->get('theme');
  }

  /**
   * Sets theme
   *
   * @param string $key
   *          The type of theme (admin/front).
   * @param string $theme
   *          The machine readable name of the theme.
   *
   * @return bool
   */
  public function setTheme($key, $theme) {
    return \Nick::Config()->set('theme.' . $key, $theme);
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