<?php

namespace Nick;

use Nick\Database\Result;

/**
 * Class Config
 *
 * @package Nick
 */
class Config extends Settings {

  /**
   * @return bool
   */
  public function import() {
    $staged = $this->getStagedConfig();
    foreach ($staged as $key => $value) {
      if (!$this->set($key, $value)) {
        \Nick::Logger()->add('Something went wrong trying to import the following config: ' . $key, Logger::TYPE_FAILURE, 'Config');
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * @return bool
   */
  public function export() {
    foreach ($this->getConfig() as $key => $value) {
      // @TODO
    }

    return TRUE;
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
      $files = scandir($folder);
      foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
          continue;
        }

        if (!is_file($folder . '/' . $file)) {
          continue;
        }

        // Add to config array.
        $key = str_replace('.yml', '', $file);
        $config[$key] = file_get_contents($folder . '/' . $file);
      }
    }
    return $config;
  }

  /**
   * @return array|bool
   */
  public function getConfig() {
    $config_storage = \Nick::Database()
      ->select('config')
      ->execute();
    if (!$config_storage instanceof Result) {
      return [];
    }

    return $config_storage->fetchAllAssoc();
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