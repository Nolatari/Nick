<?php

namespace Nick\Language;

/**
 * Class LanguageManager
 *
 * @package Nick\Language
 */
class LanguageManager {

  /** @var LanguageInterface $currentLanguage */
  protected $currentLanguage;

  /** @var LanguageInterface $defaultLanguage */
  protected $defaultLanguage;

  /**
   * Returns the website's current language
   *
   * @return LanguageInterface
   */
  public function getCurrentLanguage() {
    return $this->currentLanguage;
  }

  /**
   * Returns the website's default language
   *
   * @return LanguageInterface
   */
  public function getDefaultLanguage() {
    return $this->defaultLanguage;
  }

}