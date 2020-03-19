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
   * Returns the translated language label.
   *
   * @return string
   */
  public function getLanguage();

  /**
   * Returns either the fallback language, or the default one if
   * no fallback has been set up.
   *
   * @return LanguageInterface
   */
  public function getFallbackLanguage();

  /**
   * Returns the translated country label.
   *
   * @return string
   */
  public function getCountry();

  /**
   * Returns TRUE or FALSE whether current langcode is default language
   *
   * @return bool
   */
  public function isDefault();

}
