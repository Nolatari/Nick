<?php

namespace Nick\Translation;

use Nick;
use Nick\Database\Result;
use Nick\Event\Event;
use Nick\Language\LanguageManager;

/**
 * Class Translation
 *
 * @package Nick\Translation
 */
class Translation implements TranslationInterface {

  /** @var LanguageManager $languageManager */
  protected $languageManager;

  /**
   * Translation constructor.
   */
  public function __construct() {
    $this->languageManager = Nick::LanguageManager();
  }

  /**
   * {@inheritDoc}
   */
  public function get(string $string, $args = [], $fallback = TRUE, $langcode = NULL) {
    $langcode = !is_null($langcode) ? $langcode : $this->languageManager->getCurrentLanguage();
    $query = Nick::Database()
      ->select('translations')
      ->fields(NULL, ['translation'])
      ->condition('string', $string)
      ->condition('to_langcode', $langcode)
      ->orderBy('string', 'ASC')
      ->execute();
    if (!$query instanceof Result) {
      if ($fallback) {
        return $string;
      } else {
        return '';
      }
    }

    $result = $query->fetchAllAssoc();
    if (count($result) == 0) {
      if ($fallback) {
        return $string;
      } else {
        return '';
      }
    }

    $result = reset($result);
    foreach ($args as $arg => $replacement) {
      $result['translation'] = str_replace($arg, $replacement, $result['translation']);
    }

    return $result['translation'];
  }

  /**
   * {@inheritDoc}
   */
  public function set(string $string, string $translation, array $args = [], $from_langcode = NULL, $to_langcode = NULL) {
    $from_langcode = !is_null($from_langcode) ? $from_langcode : $this->languageManager->getDefaultLanguage();
    $to_langcode = !is_null($to_langcode) ? $to_langcode : $this->languageManager->getCurrentLanguage();

    if ($from_langcode === $to_langcode) {
      return TRUE;
    }

    // Fire an event before adding/saving the translation
    $preSaveEvent = new Event('stringTranslationPresave');
    $preSaveEvent->fire($translation, [$string, $args, $from_langcode, $to_langcode]);

    if ($this->get($string) == '') {
      $query = Nick::Database()
        ->insert('translations')
        ->values([
          'id' => 0,
          'string' => $string,
          'translation' => $translation,
          'args' => serialize($args),
          'from_langcode' => $from_langcode,
          'to_langcode' => $to_langcode,
        ])
        ->execute();
    } else {
      $query = Nick::Database()
        ->update('translations')
        ->condition('string', $string)
        ->condition('from_langcode', $from_langcode)
        ->condition('to_langcode', $to_langcode)
        ->values([
          'translation' => $translation,
          'args' => serialize($args),
        ])
        ->execute();
    }

    if (!$query) {
      return FALSE;
    }

    // Fire an event after adding/saving the translation
    $postSaveEvent = new Event('stringTranslationPostsave');
    $postSaveEvent->fire($translation, [$string, $args, $from_langcode, $to_langcode]);

    return TRUE;
  }

}
