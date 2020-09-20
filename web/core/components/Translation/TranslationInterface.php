<?php

namespace Nick\Translation;

/**
 * Interface TranslationInterface
 *
 * @package Nick\Translation
 */
interface TranslationInterface {

  /**
   * Gets translation of string if it exists.
   * If fallback is TRUE and there is no translation, it will return
   *   the original string.
   *
   * @param string      $string
   * @param array       $args
   * @param bool        $fallback
   * @param null|string $langcode
   *
   * @return string
   */
  public function get(string $string, $args = [], $fallback = TRUE, $langcode = NULL);

  /**
   * Sets string translation
   *
   * @param string      $string
   * @param string      $translation
   * @param null|string $from_langcode
   * @param null|string $to_langcode
   *
   * @return bool
   */
  public function set(string $string, string $translation, $from_langcode = NULL, $to_langcode = NULL);

}
