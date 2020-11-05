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
  public function setTheme(string $key, string $theme): bool {
    return \Nick::Config()->set('theme.' . $key, $theme);
  }

  /**
   * Returns folder where theme files/assets are located.
   *
   * @return string
   */
  public function getThemeFolder(): string {
    return $this->getBaseThemeFolder() . '/' . $this->getTheme('admin') . '/';
  }

  /**
   * Returns the folder where themes are located
   *
   * @return string
   */
  public function getBaseThemeFolder(): string {
    return Settings::get('themes.folder');
  }

  /**
   * Returns theme info array
   *
   * @param string $theme
   *               Theme name
   *
   * @return array|bool
   */
  public function getThemeInfo(string $theme) {
    if (!is_dir($this->getBaseThemeFolder() . '/' . $theme)) {
      return FALSE;
    }

    return YamlReader::fromYamlFile($this->getBaseThemeFolder() . '/' . $theme . '/' . $theme . '.yml');
  }

  /**
   * Returns available themes
   *
   * @return array
   */
  public function getAvailableThemes(): array {
    return array_map(function ($item) {
      return str_replace($this->getBaseThemeFolder() . '/', '', $item);
    }, glob($this->getBaseThemeFolder() . '/*', GLOB_ONLYDIR));
  }

}