<?php

namespace Nick;

use Nick;
use Nick\Database\Result;

/**
 * Class Config
 *
 * @package Nick
 */
class Config extends Settings {

  public function import() {
    // @TODO
  }

  public function export() {
    // @TODO
  }

  /**
   * Shows difference in config.
   *
   * @return array|bool
   */
  public function difference() {
    $config_storage = \Nick::Database()
      ->select('config')
      ->execute();
    if (!$config_storage instanceof Result) {
      return FALSE;
    }
    $results = $config_storage->fetchAllAssoc();
    $live = [];
    foreach ($results as $result) {
      $live[$result['field']] = YamlReader::toYaml(unserialize($result['value']));
    }

    return [
      'live' => $live,
      'staged' => $this->getStagedConfig(),
    ];
  }

  /**
   * @param string|NULL $name
   *
   * @return array
   */
  public function getStagedConfig($name = NULL) {
    $config = [];
    if ($name !== NULL) {
      $config[] = YamlReader::fromYamlFile($this->getSetting('config')['folder'] . '/' . $name . '.yml');
    } else {
      $folder = $this->getSetting('config')['folder'];
      if (!is_dir($folder)) {
        \Nick::Logger()->add('Config directory does not exist.', Logger::TYPE_FAILURE, 'Config');
        return $config;
      }
      $files = scandir($folder);
      foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
          continue;
        }

        if (!is_file($folder . '/' . $file)) {
          continue;
        }

        $config[] = file_get_contents($folder . '/' . $file);
      }
    }
    return $config;
  }

  /**
   * @param $key
   *
   * @return mixed
   */
  public function get($key) {
    $config_storage = \Nick::Database()
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
    $config_storage = \Nick::Database()
      ->select('config')
      ->fields(NULL, ['value'])
      ->condition('field', $key);
    /** @var Result $config_result */
    if (!$config_result = $config_storage->execute()) {
      return FALSE;
    }
    $result = $config_result->fetchAllAssoc();
    if (count($result) > 0) {
      $config_query = \Nick::Database()
        ->update('config')
        ->values(['value' => $value])
        ->condition('field', $key)
        ->execute();
    } else {
      $config_query = \Nick::Database()
        ->insert('config')
        ->values([$key, $value])
        ->execute();
    }
    return $config_query;
  }

}