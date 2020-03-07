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
    return Yaml::parseFile(__DIR__ . '../extensions/' . $ext . '/' . $ext . '.yml');
  }

  /**
   * @param string $ext
   *
   * @return mixed
   */
  public static function readCoreExtension($ext) {
    return Yaml::parseFile(__DIR__ . '/' . $ext . '/' . $ext . '.yml');
  }

}