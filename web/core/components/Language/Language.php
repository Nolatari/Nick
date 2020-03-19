<?php

namespace Nick\Language;

use Nick\Database\Result;
use Nick\Logger;
use Nick\Translation\StringTranslation;

/**
 * Class Language
 *
 * @package Nick\Language
 */
class Language implements LanguageInterface {
  use StringTranslation;

  /** @var string $langcode */
  protected $langcode;

  /** @var string $language */
  protected $language;

  /** @var bool $default */
  protected $default = FALSE;

  /**
   * Language constructor.
   *
   * @param $langcode
   */
  public function __construct($langcode = 'en') {
    if (!$this->setValues($langcode)) {
      \Nick::Logger()->add('Something went wrong trying to set language object.', Logger::TYPE_ERROR, 'Language');
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getLangcode() {
    return $this->langcode;
  }

  /**
   * {@inheritDoc}
   */
  public function getLanguage() {
    // Return translated string of language label.
    return $this->translate($this->language);
  }

  /**
   * {@inheritDoc}
   */
  public function isDefault() {
    return $this->default;
  }

  /**
   * Sets language values.
   *
   * @param string $langcode
   *
   * @return bool
   */
  protected function setValues($langcode) {
    $this->langcode = $langcode;
    if (!$properties = $this->getPropertiesByLangcode()) {
      return FALSE;
    }
    $this->language = $properties['language'];
    $this->default = $properties['default'] == 1 ? TRUE : FALSE;

    return TRUE;
  }

  /**
   * Returns info for current langcode.
   *
   * @return array|bool
   */
  protected function getPropertiesByLangcode() {
    $query = \Nick::Database()
      ->select('languages')
      ->fields(['language', 'default'])
      ->condition('langcode', $this->getLangcode())
      ->execute();
    if (!$query instanceof Result) {
      return FALSE;
    }

    return $query->fetchAllAssoc();
  }

}
