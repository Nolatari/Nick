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
  protected $currentLanguage;

  /** @var LanguageInterface $defaultLanguage */
  protected $defaultLanguage;

  /**
   * LanguageManager constructor.
   */
  public function __construct() {
    $this->defaultLanguage = Nick::Config()->get('site.default_langcode') ?? 'en';
    $this->currentLanguage = $this->defaultLanguage;
  }

  /**
   * Returns the website's current language
   *
   * @return LanguageInterface
   */
  public function getCurrentLanguage() {
    return $this->currentLanguage;
  }

  /**
   * Sets the current language
   *
   * @param string $langcode
   */
  public function setCurrentLanguage(string $langcode) {
    $this->currentLanguage = $langcode;
  }

  /**
   * Returns the website's default language
   *
   * @return LanguageInterface
   */
  public function getDefaultLanguage() {
    return $this->defaultLanguage;
  }

  /**
   * Sets the default language
   *
   * @param string $langcode
   */
  public function setDefaultLanguage(string $langcode) {
    $this->defaultLanguage = $langcode;
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
  public function getLanguageByLangcode(string $langcode = 'en') {
    return new Language($langcode);
  }

}