<?php

namespace Nick\Language;

use Exception;
use Nick;
use Nick\Logger;

/**
 * Class LanguageManager
 *
 * @package Nick\Language
 */
class LanguageManager {

  /** @var string $defaultLangcode */
  protected string $defaultLangcode;

  /** @var string currentLangcode */
  protected string $currentLangcode;

  /**
   * LanguageManager constructor.
   */
  public function __construct() {
    $this->defaultLangcode = Nick::Config()->get('site.default_langcode') ?: 'en';
    $this->currentLangcode = $this->defaultLangcode;
  }

  /**
   * Returns the website's current language
   *
   * @return LanguageInterface
   */
  public function getCurrentLanguage(): LanguageInterface {
    return new Language($this->currentLangcode);
  }

  /**
   * Sets the current language
   *
   * @param string $langcode
   */
  public function setCurrentLanguage(string $langcode) {
    $this->currentLangcode = $langcode;
  }

  /**
   * Returns the website's default language
   *
   * @return LanguageInterface
   */
  public function getDefaultLanguage(): LanguageInterface {
    return new Language($this->defaultLangcode);
  }

  /**
   * Sets the default language
   *
   * @param string $langcode
   */
  public function setDefaultLanguage(string $langcode) {
    $this->defaultLangcode = $langcode;
    try {
      Nick::Config()->set('site.default_langcode', $langcode);
    } catch (Exception $exception) {
      Nick::Logger()->add($exception, Logger::TYPE_FAILURE, 'LanguageManager');
    }
  }

  /**
   * Returns language object from given langcode.
   *
   * @param string $langcode
   *
   * @return LanguageInterface
   */
  public function getLanguageByLangcode(string $langcode = 'en'): LanguageInterface {
    return new Language($langcode);
  }

}