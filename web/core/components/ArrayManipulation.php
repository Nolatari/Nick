<?php

namespace Nick;

/**
 * Class ArrayManipulation
 *
 * @package Nick
 */
class ArrayManipulation {

  /**
   * @param array  $haystack
   *                   Haystack to search for given needle(s) in.
   * @param string $needle
   *                   Can be a string, which will be searched either as key or value depending on
   *                   the next parameter.
   * @param bool   $key
   *                   Sets whether to look in the array keys or not. Set to TRUE to look in keys,
   *                   set to FALSE to look in values.
   *
   * @return bool
   */
  public static function contains(array $haystack, string $needle, $key = FALSE): bool {
    if ($key === TRUE) {
      return isset($haystack[$needle]);
    } else {
      foreach ($haystack as $k => $v) {
        if ($v === $needle) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

}