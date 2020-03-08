<?php

namespace Nick;

use Nick;
use Nick\Database\Result;

/**
 * Class Config
 *
 * @package Nick
 */
class Config {

  /**
   * @param $key
   *
   * @return mixed
   */
  public static function get($key) {
    $config_storage = Nick::Database()
      ->select('config')
      ->fields(NULL, ['value'])
      ->condition('field', $key);
    /** @var Result $config_result */
    if (!$config_result = $config_storage->execute()) {
      return FALSE;
    }
    $result = $config_result->fetchAllAssoc();
    $result = reset($result);

    return unserialize($result['value']);
  }

  /**
   * @param string $key
   * @param string $value
   *
   * @return bool
   */
  public static function set($key, $value) {
    // Serialize value for proper database storing.
    $value = serialize($value);
    $config_storage = Nick::Database()
      ->select('config')
      ->fields(NULL, ['value'])
      ->condition('field', $key);
    /** @var Result $config_result */
    if (!$config_result = $config_storage->execute()) {
      return FALSE;
    }
    $result = $config_result->fetchAllAssoc();
    if (count($result) > 0) {
      $config_query = Nick::Database()
        ->update('config')
        ->values(['value' => $value])
        ->condition('field', $key)
        ->execute();
    } else {
      $config_query = Nick::Database()
        ->insert('config')
        ->values([$key, $value])
        ->execute();
    }
    return $config_query;
  }

}