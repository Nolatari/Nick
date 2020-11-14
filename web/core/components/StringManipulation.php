<?php

namespace Nick;

/**
 * Class StringManipulation
 *
 * @package Nick
 */
class StringManipulation {

  /**
   * Checks whether string contains a given needle
   *
   * @param string       $haystack
   *                         Haystack to search for given needle(s) in.
   * @param string|array $needle
   *                         Can be a string or an array of strings.
   *
   * @return bool
   */
  public static function contains(string $haystack, $needle): bool {
    if (is_string($needle)) {
      return strpos($haystack, $needle) !== FALSE;
    } elseif (is_array($needle)) {
      foreach ($needle as $key => $value) {
        return strpos($haystack, $value) !== FALSE || strpos($haystack, $key) !== FALSE;
      }
      return FALSE;
    } else {
      return FALSE;
    }
  }

  /**
   * Copy of preg_replace
   *
   * @param string $haystack
   *                         Haystack to search for given needle(s) in.
   * @param string $pattern
   *                         Regex pattern in string format
   * @param string $replacement
   *                         The replacement for the needle(s) given.
   *
   * @return string|string[]
   */
  public static function preg_replace(string $haystack, string $pattern, string $replacement) {
    return preg_replace($pattern, $replacement, $haystack);
  }

  /**
   * Copy of str_replace
   *
   * @param string       $haystack
   *                         Haystack to search for given needle(s) in.
   * @param string|array $needle
   *                         Can be a string or an array of strings, but every instance of these strings will be
   *                         replaced with a single given replacement string.
   * @param string       $replacement
   *                         The replacement for the needle(s) given.
   *
   * @return string|string[]
   */
  public static function replace(string $haystack, $needle, string $replacement): string {
    if (is_string($needle) || is_array($needle)) {
      return str_replace($needle, $replacement, $haystack);
    } else {
      return $haystack;
    }
  }

  /**
   * Explodes string into parts by given delimiter.
   *
   * @param string $haystack
   *                  Haystack to search for given needle in.
   * @param string $needle
   *                  Needle as string
   * @param int    $limit
   *                  The max amount of splits, default 99999 (null doesn't seem to work)
   *
   * @return array
   */
  public static function explode(string $haystack, string $needle, int $limit = 99999): array {
    return explode($needle, $haystack, $limit);
  }

  /**
   * Capitalizes first letter of a given string.
   *
   * @param string $text
   *
   * @return string
   */
  public static function capitalize(string $text) {
    return ucfirst($text);
  }

  /**
   * Capitalizes first letter of each word in given string.
   *
   * @param string $text
   *
   * @return string
   */
  public static function capitalizeWords(string $text) {
    return ucwords($text);
  }

  /**
   * Uncapitalizes first letter of a given string.
   *
   * @param string $text
   *
   * @return string
   */
  public static function uncapitalize(string $text) {
    return lcfirst($text);
  }

  /**
   * Turns string into uppercase
   *
   * @param string $text
   *
   * @return string
   */
  public static function uppercase(string $text) {
    return strtoupper($text);
  }

  /**
   * Turns string into lowercase
   *
   * @param string $text
   *
   * @return string
   */
  public static function lowercase(string $text) {
    return strtolower($text);
  }

}