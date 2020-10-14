<?php

namespace Nick\Config;

use Exception;
use FilesystemIterator;
use Nick;
use Nick\Database\Result;
use Nick\Logger;
use Nick\Settings;
use Nick\StringManipulation;
use Nick\Translation\StringTranslation;
use Nick\YamlReader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class Config
 *
 * @package Nick
 */
class Config {
  use StringTranslation;

  /**
   * @return bool
   */
  public function import() {
    if (!Nick::Database()->query('TRUNCATE TABLE config')) {
      Nick::Logger()->add('Something went wrong trying to truncate the config table.', Logger::TYPE_FAILURE, 'Config');
      return FALSE;
    }

    $staged = $this->getStagedConfig();
    foreach ($staged as $key => $value) {
      if ($key === 'extensions') {
        foreach ($value as $extension) {
          Nick::ExtensionManager()::installExtension($extension['name'], $extension['type']);
        }
      } else {
        if (!$this->set($key, $value)) {
          Nick::Logger()->add(
            $this->translate('Something went wrong trying to import the following config: :key', [':key' => $key]), Logger::TYPE_FAILURE, 'Config');
          return FALSE;
        }
      }
    }

    return TRUE;
  }

  /**
   * @param string|NULL $name
   *
   * @return array
   */
  public function getStagedConfig($name = NULL) {
    $config = [];
    if ($name !== NULL) {
      $config[] = YamlReader::fromYamlFile(Settings::get('config.folder') . '/' . $name . '.yml');
    } else {
      $folder = Settings::get('config.folder');
      if (!is_dir($folder)) {
        Nick::Logger()->add($this->translate('Config directory does not exist. Please create this folder, and give it write rights.'), Logger::TYPE_ERROR, 'Config');
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

        // Add to config array.
        $key = str_replace('.yml', '', $file);
        $config[$key] = YamlReader::fromYamlFile($folder . '/' . $file);
      }
    }
    return $config;
  }

  /**
   * @return bool
   */
  public function export() {
    $config_folder = Settings::get('config.folder');
    if (!is_dir(Settings::get('config.folder'))) {
      Nick::Logger()->add($this->translate('Config directory does not exist. Please create this folder, and give it writing rights.'), Logger::TYPE_ERROR, 'Config');
    }
    $di = new RecursiveDirectoryIterator($config_folder, FilesystemIterator::SKIP_DOTS);
    $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($ri as $file) {
      if ($file->isDir()) {
        continue;
      }
      unlink($file);
    }

    foreach ($this->getConfig() as $item) {
      $config = YamlReader::toYaml(unserialize($item['value']));
      try {
        $config_file = fopen(Settings::get('config.folder') . '/' . $item['field'] . '.yml', 'w');
        fwrite($config_file, $config);
        fclose($config_file);
      } catch (Exception $e) {
        Nick::Logger()->add(
          $this->translate('Something went wrong trying to write config to file. [:field.yml]', [':field' => $item['field']]),
          Logger::TYPE_ERROR,
          'Config'
        );
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * @return array|bool
   */
  public function getConfig() {
    $config_storage = Nick::Database()
      ->select('config')
      ->execute();
    if (!$config_storage instanceof Result) {
      $returnArray = [];
    } else {
      $returnArray = $config_storage->fetchAllAssoc();
    }

    $returnArray[] = ['field' => 'extensions', 'value' => serialize(Nick::ExtensionManager()::getInstalledExtensions())];

    return $returnArray;
  }

  /**
   * Shows difference in config.
   *
   * @return array|bool
   */
  public function difference() {
    return [
      'live' => $this->getConfig(),
      'staged' => $this->getStagedConfig(),
    ];
  }

  /**
   * @param $key
   *
   * @return string|array
   */
  public function get($key) {
    if (StringManipulation::contains($key, '.')) {
      $items = explode('.', $key);
      $key = $items[0];
      unset($items[0]);
    }
    $config_storage = Nick::Database()
      ->select('config')
      ->fields(NULL, ['value'])
      ->condition('field', $key);
    /** @var Result $config_result */
    if (!$config_result = $config_storage->execute()) {
      return FALSE;
    }
    $result = $config_result->fetchAllAssoc();
    if (!$result) {
      return FALSE;
    }
    $result = reset($result);

    $config = unserialize($result['value']);

    if (isset($items)) {
      foreach ($items as $item) {
        $config = $config[$item];
      }
    }

    return $config;
  }

  /**
   * @param string       $key
   * @param array|string $value
   *
   * @return bool
   */
  public function set(string $key, $value) {
    if (strpos($key, '.') !== FALSE) {
      $items = explode('.', $key);
      $key = $items[0];
      $item = $items[1];
    }

    $config_storage = Nick::Database()
      ->select('config')
      ->fields(NULL, ['value'])
      ->condition('field', $key)
      ->execute();
    if (!$config_storage instanceof Result) {
      return FALSE;
    }
    $result = $config_storage->fetchAllAssoc();
    if (count($result) > 0) {
      $result = reset($result);
      if (isset($item)) {
        $result[$item] = $value;
        $value = $result;
      }

      $value = serialize($value);
      $config_query = Nick::Database()
        ->update('config')
        ->values(['value' => $value])
        ->condition('field', $key)
        ->execute();
    } else {
      if (isset($item)) {
        $result = [$item => $value];
        $value = $result;
      }

      $value = serialize($value);
      $config_query = Nick::Database()
        ->insert('config')
        ->values([$key, $value])
        ->execute();
    }
    return $config_query;
  }

}