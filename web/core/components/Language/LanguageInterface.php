<?php

namespace Nick\Language;

/**
 * Interface LanguageInterface
 *
 * @package Nick\Language
 */
interface LanguageInterface {

  /**
   * Returns language code.
   *
   * @return string
   */
  public function getLangcode();

  /**
   * Returns the language label.
   *
   * @return string
   */
  public function getLanguage();

  /**
   * Returns TRUE or FALSE whether current langcode is default language
   *
   * @return bool
   */
  public function isDefault();

}
