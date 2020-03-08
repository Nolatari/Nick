<?php

namespace Nick;

/**
 * Class Theme
 *
 * @package Nick
 */
class Theme {

  public function getTheme() {
    return \Nick::Config()->get('admin_theme');
  }

  /**
   * Sets theme
   *
   * @param string $theme
   *
   * @return bool
   */
  public function setTheme($theme) {
    return \Nick::Config()->set('admin_theme', $theme);
  }

  /**
   * @return string
   */
  public function getThemeFolder() {
    return 'themes/' . $this->getTheme() . '/';
  }

}