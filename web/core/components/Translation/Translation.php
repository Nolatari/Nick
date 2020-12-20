<?php

namespace Nick\Translation;

use Nick;
use Nick\Database\Result;
use Nick\Language\LanguageManager;
use Nick\StringManipulation;

/**
 * Class Translation
 *
 * @package Nick\Translation
 */
class Translation implements TranslationInterface {

  /** @var LanguageManager $languageManager */
  protected LanguageManager $languageManager;

  /**
   * Translation constructor.
   */
  public function __construct() {
    $this->languageManager = \Nick::LanguageManager();
  }

  /**
   * {@inheritDoc}
   */
  public function get(string $string, $args = [], $fallback = TRUE, $langcode = NULL): string {
    $langcode = $langcode ?: $this->languageManager->getCurrentLanguage()->getLangcode();
    $query = \Nick::Database()
      ->select('translations')
      ->fields(NULL, ['translation'])
      ->condition('string', $string)
      ->condition('to_langcode', $langcode)
      ->orderBy('string', 'ASC')
      ->execute();
    if (!$query instanceof Result) {
      if ($fallback) {
        return $this->replaceArgs($string, $args);
      } else {
        return '';
      }
    }

    $result = $query->fetchAllAssoc();
    if (count($result) == 0) {
      if ($fallback) {
        return $this->replaceArgs($string, $args);
      } else {
        return '';
      }
    }

    $result = reset($result);
    return $this->replaceArgs($result['translation'], $args);
  }

  /**
   * {@inheritDoc}
   */
  public function set(string $string, string $translation, $from_langcode = NULL, $to_langcode = NULL): bool {
    $from_langcode = $from_langcode ?? $this->languageManager->getDefaultLanguage()->getLangcode();
    $to_langcode = $to_langcode ?? $this->languageManager->getCurrentLanguage()->getLangcode();

    if ($from_langcode === $to_langcode) {
      // Does not need to be translated because from and to langcode are the same.
      return TRUE;
    }

    // Fire an event before adding/saving the translation
    \Nick::Event('stringTranslationPresave')
      ->fire($translation, [$string, $from_langcode, $to_langcode]);

    if ($this->get($string, [], FALSE) == '') {
      $query = \Nick::Database()
        ->insert('translations')
        ->values([
          'id' => 0,
          'string' => $string,
          'translation' => $translation,
          'from_langcode' => $from_langcode,
          'to_langcode' => $to_langcode,
        ])
        ->execute();
    } else {
      $query = \Nick::Database()
        ->update('translations')
        ->condition('string', $string)
        ->condition('from_langcode', $from_langcode)
        ->condition('to_langcode', $to_langcode)
        ->values([
          'translation' => $translation,
        ])
        ->execute();
    }

    if (!$query) {
      return FALSE;
    }

    // Fire an event after adding/saving the translation
    \Nick::Event('stringTranslationPostsave')
      ->fire($translation, [$string, $from_langcode, $to_langcode]);

    return TRUE;
  }

  /**
   * @param string $string
   * @param array  $args
   *
   * @return string|string[]
   */
  public function replaceArgs(string $string, array $args) {
    $returnString = $string;
    foreach ($args as $arg => $replacement) {
      $returnString = StringManipulation::replace($returnString, $arg, $replacement);
    }
    return $returnString;
  }

}
