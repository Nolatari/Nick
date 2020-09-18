<?php

namespace Nick;

/**
 * Class StringManipulation
 *
 * @package Nick
 */
class StringManipulation {

  /**
   * @param string $string
   * @param string $needle
   *
   * @return bool
   */
  public static function contains(string $string, string $needle): bool {
    if (strpos($string, $needle) !== FALSE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * @param string $string
   * @param string $key
   * @param string $replacement
   *
   * @return string|string[]
   */
  public static function replace(string &$string, string $key, string $replacement): string {
    return str_replace($key, $replacement, $string);
  }

}