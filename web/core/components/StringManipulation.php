<?php

namespace Nick;

/**
 * Class StringManipulation
 *
 * @package Nick
 */
class StringManipulation {

  /**
   * @param string       $haystack
   *                         Haystack to search for given needle(s) in.
   * @param string|array $needle
   *                         Can be a string or an array of strings.
   *
   * @return bool
   */
  public static function contains(string $haystack, $needle): bool {
    if (is_string($needle)) {
      if (strpos($haystack, $needle) !== FALSE) {
        return TRUE;
      }
      return FALSE;
    } elseif (is_array($needle)) {
      foreach ($needle as $item) {
        if (strpos($haystack, $item) !== FALSE) {
          return TRUE;
        }
      }
      return FALSE;
    } else {
      return FALSE;
    }
  }

  /**
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
    if (is_string($needle)) {
      return str_replace($needle, $replacement, $haystack);
    } elseif (is_array($needle)) {
      $returnString = $haystack;
      foreach ($needle as $item) {
        $returnString = str_replace($item, $replacement, $returnString);
      }
      return $returnString;
    } else {
      return $haystack;
    }
  }

  /**
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

}