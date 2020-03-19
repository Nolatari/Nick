<?php

namespace Nick\Translation;

use Nick\Database\Result;
use Nick\Events\Event;
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
    $this->languageManager = \Nick::LanguageManager();
  }

  /**
   * {@inheritDoc}
   */
  public function get($string, $fallback = TRUE, $langcode = NULL) {
    $langcode = !is_null($langcode) ? $langcode : $this->languageManager->getCurrentLanguage();
    $query = \Nick::Database()
      ->select('translations')
      ->fields(['translation', 'args'])
      ->condition('string', $string)
      ->condition('to_langcode', $langcode)
      ->execute();
    if ($query instanceof Result) {
      $result = $query->fetchAllAssoc();
      if (count($result) == 0) {
        if ($fallback) {
          return $string;
        } else {
          return '';
        }
      }

      // There should be only one result considering the string field is unique in DB.
      $result = reset($result);
      $args = unserialize($result['args']);
      foreach ($args as $arg => $replacement) {
        $result['translation'] = str_replace($arg, $replacement, $result['translation']);
      }

      return $result['translation'];
    }

    if ($fallback) {
      return $string;
    } else {
      return '';
    }
  }


  /**
   * {@inheritDoc}
   */
  public function set($string, $translation, array $args = [], $from_langcode = NULL, $to_langcode = NULL) {
    $from_langcode = !is_null($from_langcode) ? $from_langcode : $this->languageManager->getDefaultLanguage();
    $to_langcode = !is_null($to_langcode) ? $to_langcode : $this->languageManager->getCurrentLanguage();

    // Fire an event before adding/saving the translation
    $preSaveEvent = new Event('stringTranslationPresave');
    $preSaveEvent->fireEvent($translation, [$string, $args, $from_langcode, $to_langcode]);

    if ($string === $this->get($string, TRUE)) {
      $query = \Nick::Database()
        ->insert('translatable_strings')
        ->values([
          'string' => $string,
          'translation' => $translation,
          'args' => serialize($args),
          'langcode_from' => $from_langcode,
          'langcode_to' => $to_langcode,
        ])
        ->execute();
    } else {
      $query = \Nick::Database()
        ->update('translatable_strings')
        ->condition('string', $string)
        ->condition('langcode_from', $from_langcode)
        ->condition('langcode_to', $to_langcode)
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
    $postSaveEvent->fireEvent($translation, [$string, $args, $from_langcode, $to_langcode]);

    return TRUE;
  }

}
