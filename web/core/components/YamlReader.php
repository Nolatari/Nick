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
    return self::readFile(__DIR__ . '../extensions/' . $ext . '/' . $ext . '.yml');
  }

  /**
   * @param string $ext
   *
   * @return mixed
   */
  public static function readCoreExtension($ext) {
    return self::readFile(__DIR__ . '/' . $ext . '/' . $ext . '.yml');
  }

  /**
   * @param $file
   *
   * @return array|mixed
   */
  protected static function readFile($file) {
    if (!is_file($file)) {
      return ['type' => 'undefined'];
    }
    return Yaml::parseFile($file);
  }

}