<?php

namespace Nick;

use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlReader
 *
 * @package Nick
 */
class YamlReader {

  /**
   * Reads yml files from the extension, default will be 'info'
   *
   * @param string $extension
   *                  The extension to be read
   * @param string $type
   *                  The type of file (info, routing, services)
   *
   * @return array|false|mixed|string[]
   */
  public static function readExtension(string $extension, string $type = 'info') {
    $contrib_file = __DIR__ . '/../../extensions/' . $extension . '/extension.' . $type . '.yml';
    $core_file = __DIR__ . '/' . $extension . '/extension.' . $type . '.yml';
    if (is_file($contrib_file)) {
      $file = $contrib_file;
    } elseif (is_file($core_file)) {
      $file = $core_file;
    } else {
      return FALSE;
    }
    return self::fromYamlFile($file);
  }

  /**
   * Reads core yml files, default will be 'info'
   *
   * @param string $type
   *                  The type of file (info, routing, services)
   *
   * @return array|false|mixed|string[]
   */
  public static function readCore(string $type = 'info') {
    if (!is_file(__DIR__ . '/core.' . $type . '.yml')) {
      return FALSE;
    }

    return self::fromYamlFile(__DIR__ . '/core.' . $type . '.yml');
  }

  /**
   * Convert yaml file to PHP.
   *
   * @param string $file
   *
   * @return mixed|string[]
   */
  public static function fromYamlFile(string $file) {
    if (!is_file($file)) {
      return ['type' => 'undefined'];
    }
    return Yaml::parseFile($file);
  }

  /**
   * Convert yaml string to php.
   *
   * @param string $content
   *
   * @return mixed
   */
  public static function fromYaml(string $content) {
    return Yaml::parse($content);
  }

  /**
   * Convert php to yaml string.
   *
   * @param $content
   *
   * @return string
   */
  public static function toYaml($content) {
    return Yaml::dump($content);
  }

}