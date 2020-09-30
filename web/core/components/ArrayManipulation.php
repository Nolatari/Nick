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

  /**
   * Will remove array entries with an empty value.
   * Empty can be null, an empty string or empty array.
   *
   * @param array $array
   *                 The array where empty entries should be removed
   *
   * @return array $array
   */
  public static function removeEmptyEntries(array &$array): array {
    foreach ($array as $key => $item) {
      if (is_string($item)) {
        if ($item === '') {
          unset($array[$key]);
        }
      } elseif (is_array($item)) {
        if (count($item) === 0) {
          unset($array[$key]);
        }
      } elseif (is_null($item)) {
        unset($array[$key]);
      }
    }
    return $array;
  }

}