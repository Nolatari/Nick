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
   * @return mixed
   */
  public static function readExtension(string $extension, string $type = 'info') {
    if (is_file(__DIR__ . '/../../extensions/' . $extension . '/extension.' . $type . '.yml')) {
      $file = __DIR__ . '/../../extensions/' . $extension . '/extension.' . $type . '.yml';
    } elseif (is_file(__DIR__ . '/' . $extension . '/extension.' . $type . '.yml')) {
      $file = __DIR__ . '/' . $extension . '/extension.' . $type . '.yml';
    } else {
      return FALSE;
    }
    return self::fromYamlFile($file);
  }

  /**
   * Yaml file => PHP
   *
   * @param string $file
   *
   * @return array|mixed
   */
  public static function fromYamlFile(string $file) {
    if (!is_file($file)) {
      return ['type' => 'undefined'];
    }
    return Yaml::parseFile($file);
  }

  /**
   * Yaml => PHP
   *
   * @param string $content
   *
   * @return array|mixed
   */
  public static function fromYaml(string $content) {
    return Yaml::parse($content);
  }

  /**
   * PHP => Yaml
   *
   * @param $content
   *
   * @return string
   */
  public static function toYaml($content) {
    return Yaml::dump($content);
  }

}