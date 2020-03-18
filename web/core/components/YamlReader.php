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
   * @return mixed
   */
  public static function readExtension($ext) {
    if (is_file(__DIR__ . '/../../extensions/' . $ext . '/' . $ext . '.yml')) {
      $file = __DIR__ . '/../../extensions/' . $ext . '/' . $ext . '.yml';
    } elseif (is_file(__DIR__ . '/' . $ext . '/' . $ext . '.yml')) {
      $file = __DIR__ . '/' . $ext . '/' . $ext . '.yml';
    } else {
      return FALSE;
    }
    return self::fromYamlFile($file);
  }

  /**
   * Yaml file => PHP
   *
   * @param $file
   *
   * @return array|mixed
   */
  public static function fromYamlFile($file) {
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
  public static function fromYaml($content) {
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