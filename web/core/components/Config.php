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

  public function import() {
    // @TODO
  }

  public function export() {
    // @TODO
  }

  /**
   * Shows difference in config.
   *
   * @return bool
   */
  public function difference() {
    $config_storage = \Nick::Database()
      ->select('config')
      ->execute();
    if (!$config_storage instanceof Result) {
      return FALSE;
    }
    $results = $config_storage->fetchAllAssoc();
    $config = [];
    foreach ($results as $result) {
      $config[$result['field']] = YamlReader::toYaml(unserialize($result['value']));
    }
    d($config);
  }

  /**
   * @param $key
   *
   * @return mixed
   */
  public function get($key) {
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
  public function set($key, $value) {
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