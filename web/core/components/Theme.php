<?php

namespace Nick;

use Nick;

/**
 * Class Theme
 *
 * @package Nick
 */
class Theme {

  /**
   * Returns theme from config.
   *
   * @param null|string $key
   *          The type of theme (admin/front).
   *
   * @return mixed
   */
  public function getTheme($key = NULL): string {
    if ($key !== NULL) {
      return Nick::Config()->get('theme.' . $key);
    }
    return Nick::Config()->get('theme');
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
  public function setTheme(string $key, string $theme): bool {
    return Nick::Config()->set('theme.' . $key, $theme);
  }

  /**
   * Returns folder where theme files/assets are located.
   *
   * @return string
   */
  public function getThemeFolder(): string {
    return 'themes/' . $this->getTheme('admin') . '/';
  }

}