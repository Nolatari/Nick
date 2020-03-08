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
    return self::fromYamlFile(__DIR__ . '/../../extensions/' . $ext . '/' . $ext . '.yml');
  }

  /**
   * @param string $ext
   *
   * @return mixed
   */
  public static function readCoreExtension($ext) {
    return self::fromYamlFile(__DIR__ . '/' . $ext . '/' . $ext . '.yml');
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