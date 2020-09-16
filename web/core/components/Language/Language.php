<?php

namespace Nick\Language;

use Nick;
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

  /** @var LanguageInterface $fallbackLanguage */
  protected $fallbackLanguage;

  /** @var string $country */
  protected $country;

  /** @var bool $default */
  protected $default = FALSE;

  /**
   * Language constructor.
   *
   * @param $langcode
   */
  public function __construct($langcode = 'en') {
    if (!$this->setValues($langcode)) {
      Nick::Logger()->add('Something went wrong trying to set language object.', Logger::TYPE_ERROR, 'Language');
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getLangcode(): string {
    return $this->langcode;
  }

  /**
   * {@inheritDoc}
   */
  public function getLanguage(): string {
    // Return translated string of language label.
    return $this->translate(':language', [':language' => $this->language]);
  }

  /**
   * {@inheritDoc}
   */
  public function getFallbackLanguage(): string {
    return $this->fallbackLanguage;
  }

  /**
   * {@inheritDoc}
   */
  public function getCountry(): string {
    // Return translated string of language label.
    return $this->translate(':country', [':country' => $this->country]);
  }

  /**
   * {@inheritDoc}
   */
  public function isDefault(): bool {
    return $this->default;
  }

  /**
   * Sets language values.
   *
   * @param string $langcode
   *
   * @return bool
   */
  protected function setValues(string $langcode): bool {
    $this->langcode = $langcode;
    if (!$properties = $this->getProperties()) {
      return FALSE;
    }
    $this->language = $properties['language'];
    $this->fallbackLanguage = !empty($properties['fallback'])
      ? Nick::LanguageManager()->getLanguageByLangcode($properties['fallback'])
      : Nick::LanguageManager()->getDefaultLanguage();
    $this->country = $properties['country'];
    $this->default = Nick::Config()->get('site.default_langcode') == $langcode;

    return TRUE;
  }

  /**
   * Returns info for current langcode.
   *
   * @return array|bool
   */
  protected function getProperties(): array {
    $query = Nick::Database()
      ->select('languages')
      ->fields(NULL, ['language', 'country'])
      ->condition('langcode', $this->getLangcode())
      ->execute();
    if (!$query instanceof Result) {
      return [];
    }

    return $query->fetchAllAssoc();
  }

}
