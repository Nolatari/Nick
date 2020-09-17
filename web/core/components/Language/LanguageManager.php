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

  /** @var LanguageInterface $currentLanguage */
  protected LanguageInterface $currentLanguage;

  /** @var LanguageInterface $defaultLanguage */
  protected LanguageInterface $defaultLanguage;

  /**
   * LanguageManager constructor.
   */
  public function __construct() {
    $this->defaultLanguage = $this->getLanguageByLangcode(Nick::Config()->get('site.default_langcode') ?? 'en');
    $this->currentLanguage = $this->defaultLanguage;
  }

  /**
   * Returns the website's current language
   *
   * @return LanguageInterface
   */
  public function getCurrentLanguage(): LanguageInterface {
    return $this->currentLanguage;
  }

  /**
   * Sets the current language
   *
   * @param string $langcode
   */
  public function setCurrentLanguage(string $langcode) {
    $this->currentLanguage = $this->getLanguageByLangcode($langcode);
  }

  /**
   * Returns the website's default language
   *
   * @return LanguageInterface
   */
  public function getDefaultLanguage(): LanguageInterface {
    return $this->defaultLanguage;
  }

  /**
   * Sets the default language
   *
   * @param string $langcode
   */
  public function setDefaultLanguage(string $langcode) {
    $this->defaultLanguage = $this->getLanguageByLangcode($langcode);
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