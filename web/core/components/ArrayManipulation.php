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
   * Copies array item from one key to another, and keeps the old array entry.
   *
   * @param array $array
   * @param mixed $originalKey
   * @param mixed $newKey
   *
   * @usage $myArray = ['my-old-key' => 'Default value'];
   *        ArrayManipulation::copyKey($myArray, 'my-old-key', 'new-key-123');
   *    Result:
   *        $myArray === ['new-key-123' => 'Default value', 'new-key-123' => 'Default value']
   *
   * @return array
   */
  public static function copyKey(array &$array, $originalKey, $newKey) {
    if (!isset($array[$originalKey])) {
      return $array;
    }

    $array[$newKey] = &$array[$originalKey];
    return $array;
  }

  /**
   * Moves array item from one key to another, and unsets the old array entry.
   *
   * @param array $array
   * @param mixed $originalKey
   * @param mixed $newKey
   *
   * @usage $myArray = ['my-old-key' => 'Default value'];
   *        ArrayManipulation::moveKey($myArray, 'my-old-key', 'new-key-123');
   *    Result:
   *        $myArray === ['new-key-123' => 'Default value']
   *
   * @return array
   */
  public static function moveKey(array &$array, $originalKey, $newKey) {
    if (!isset($array[$originalKey])) {
      return $array;
    }

    $array[$newKey] = &$array[$originalKey];
    unset($array[$originalKey]);
    return $array;
  }

  /**
   * Will remove array entries with an empty value.
   * Empty can be null, an empty string or empty array.
   *
   * @param array $array
   *                 The array where empty entries should be removed
   * @param bool  $recursive
   *                 Default: TRUE, whether to also remove empty entries from arrays inside main array recursively
   * @param bool  $resetKeys
   *                 Default: FALSE, will reset keys in array after removing entries
   *
   * @return array $array
   */
  public static function removeEmptyEntries(array $array, bool $recursive = TRUE, bool $resetKeys = FALSE): array {
    foreach ($array as $key => $item) {
      if (is_string($item)) {
        if ($item === '') {
          unset($array[$key]);
        }
      } elseif (is_array($item)) {
        if (count($item) === 0) {
          unset($array[$key]);
        } else {
          if ($recursive) {
            $array[$key] = static::removeEmptyEntries($array[$key], $recursive, $resetKeys);
          }
        }
      } elseif (is_null($item)) {
        unset($array[$key]);
      }
    }
    if ($resetKeys) {
      return array_merge($array);
    }
    return $array;
  }

}