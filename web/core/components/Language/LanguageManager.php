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
    $this->setLangcode('default', \Nick::Config()->get('site.default_langcode') ?: 'en');
    $this->setLangcode('current', \Nick::CurrentPerson()->getLanguage() ?: $this->getLangcode('default'));
  }

  /**
   * Returns the website's current language
   *
   * @return LanguageInterface
   */
  public function getCurrentLanguage(): LanguageInterface {
    return $this->getLanguageByLangcode($this->getLangcode('current'));
  }

  /**
   * Sets the current language for the current user.
   *
   * @param string $langcode
   */
  public function setCurrentLanguage(string $langcode) {
    $this->setLangcode('current', $langcode);
    try {
      \Nick::CurrentPerson()->setValue('language', $langcode)->save();
    } catch (Exception $exception) {
      \Nick::Logger()->add($exception, Logger::TYPE_FAILURE, 'LanguageManager');
    }
  }

  /**
   * Returns langcode of either default or current type
   *
   * @param string $type
   *
   * @return array|false|string|null
   */
  public function getLangcode(string $type) {
    switch ($type) {
      case 'default':
        return $this->defaultLangcode;
      case 'current':
        return $this->currentLangcode;
    }

    return NULL;
  }

  /**
   * Sets langcode of either default or current type
   *
   * @param string $type
   * @param string $langcode
   *
   * @return array|false|string|null
   */
  public function setLangcode(string $type, string $langcode) {
    switch ($type) {
      case 'default':
        $this->defaultLangcode = $langcode;
        break;
      case 'current':
        $this->currentLangcode = $langcode;
        break;
    }

    return $this;
  }

  /**
   * Returns the website's default language
   *
   * @return LanguageInterface
   */
  public function getDefaultLanguage(): LanguageInterface {
    return $this->getLanguageByLangcode($this->getLangcode('default'));
  }

  /**
   * Sets the default language for the whole website.
   *
   * @param string $langcode
   */
  public function setDefaultLanguage(string $langcode) {
    $this->setLangcode('default', $langcode);
    try {
      \Nick::Config()->set('site.default_langcode', $langcode);
    } catch (Exception $exception) {
      \Nick::Logger()->add($exception->getMessage(), Logger::TYPE_FAILURE, 'LanguageManager');
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
    $query = \Nick::Database()
      ->select('languages')
      ->fields(NULL, ['langcode', 'language', 'country'])
      ->execute();
    if (!$query instanceof Result) {
      return FALSE;
    }

    return $query->fetchAllAssoc('langcode');
  }

}