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
   * @param string $ext
   *
   * @param string $type
   *
   * @return mixed
   */
  public static function readExtension(string $ext, $type = 'info') {
    if (is_file(__DIR__ . '/../../extensions/' . $ext . '/extension.' . $type . '.yml')) {
      $file = __DIR__ . '/../../extensions/' . $ext . '/extension.' . $type . '.yml';
    } elseif (is_file(__DIR__ . '/' . $ext . '/extension.' . $type . '.yml')) {
      $file = __DIR__ . '/' . $ext . '/extension.' . $type . '.yml';
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