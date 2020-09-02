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
   * @param string $string
   * @param bool   $fallback
   * @param null   $langcode
   *
   * @return string
   */
  public function get($string, $fallback = TRUE, $langcode = NULL);

  /**
   * Sets string translation
   *
   * @param string $string
   * @param string $translation
   * @param array  $args
   * @param string $from_langcode
   * @param string $to_langcode
   *
   * @return bool
   */
  public function set($string, $translation, array $args = [], $from_langcode = NULL, $to_langcode = NULL);

}
