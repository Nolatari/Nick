<?php

namespace Nick\Translation;

use Nick\Database\Result;
use Nick\Logger;

/**
 * Class Translation
 *
 * @package Nick\Translation
 */
class Translation implements TranslationInterface {

  /**
   * @inheritDoc
   */
  public function translate($string, array $args = []) {
    if (!is_string($string)) {
      \Nick::Logger()->add('Only strings should be entered.');
      return FALSE;
    }

    if ($string === $this->get($string, TRUE)) {
      if (!$this->set($string, $string, $args)) {
        \Nick::Logger()->add('Something went wrong trying to set a translation.', Logger::TYPE_FAILURE, 'Translation');
      }
    }
    return $this->get($string, TRUE);
  }

  /**
   * Gets translation of string if it exists.
   * If fallback is TRUE and there is no translation, it will return
   *   the original string.
   *
   * @param string $string
   * @param bool $fallback
   *
   * @return string
   */
  protected function get($string, $fallback = TRUE) {
    $query = \Nick::Database()
      ->select('translations')
      ->fields(['translation', 'args'])
      ->condition('string', $string)
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
   * Sets string translation
   *
   * @param string $string
   * @param string $translation
   * @param array $args
   *
   * @return bool
   */
  protected function set($string, $translation, array $args = []) {
    if ($string === $this->get($string, TRUE)) {
      $query = \Nick::Database()
        ->insert('translations')
        ->values([
          'string' => $string,
          'translation' => $translation,
          'args' => serialize($args),
        ])
        ->execute();
    } else {
      $query = \Nick::Database()
        ->update('translations')
        ->condition('string', $string)
        ->values([
          'translation' => $translation,
          'args' => serialize($args),
        ])
        ->execute();
    }

    if (!$query) {
      return FALSE;
    }

    return TRUE;
  }

}
