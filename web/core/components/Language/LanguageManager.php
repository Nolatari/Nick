<?php

namespace Nick\Language;

use Exception;
use Nick;
use Nick\Database\Result;
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
    $this->defaultLangcode = \Nick::Config()->get('site.default_langcode') ?: 'en';
    $this->currentLangcode = \Nick::CurrentPerson()->getLanguage() ?: $this->defaultLangcode;
  }

  /**
   * Returns the website's current language
   *
   * @return LanguageInterface
   */
  public function getCurrentLanguage(): LanguageInterface {
    return $this->getLanguageByLangcode($this->currentLangcode);
  }

  /**
   * Sets the current language for the current user.
   *
   * @param string $langcode
   */
  public function setCurrentLanguage(string $langcode) {
    $this->currentLangcode = $langcode;
    try {
      Nick::CurrentPerson()->setValue('language', $langcode)->save();
    } catch (Exception $exception) {
      Nick::Logger()->add($exception, Logger::TYPE_FAILURE, 'LanguageManager');
    }
  }

  /**
   * Returns the website's default language
   *
   * @return LanguageInterface
   */
  public function getDefaultLanguage(): LanguageInterface {
    return $this->getLanguageByLangcode($this->defaultLangcode);
  }

  /**
   * Sets the default language for the whole website.
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
  public function getLanguageByLangcode(string $langcode): LanguageInterface {
    return \Nick::Language($langcode);
  }

  /**
   * Returns all available languages.
   *
   * @return array|bool
   */
  public function getAvailableLanguages() {
    $query = Nick::Database()
      ->select('languages')
      ->fields(NULL, ['langcode', 'language', 'country'])
      ->execute();
    if (!$query instanceof Result) {
      return FALSE;
    }

    return $query->fetchAllAssoc('langcode');
  }

}