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
    $contrib_extension = __DIR__ . '/../../extensions/' . $extension . '/extension.' . $type . '.yml';
    $core_extension = __DIR__ . '/../extensions/' . $extension . '/extension.' . $type . '.yml';
    if (is_file($contrib_extension)) {
      $file = $contrib_extension;
    } elseif (is_file($core_extension)) {
      $file = $core_extension;
    } else {
      return FALSE;
    }
    return self::fromYamlFile($file);
  }

  /**
   * Reads yml files from the component, default will be 'info'
   *
   * @param string $component
   *                  The component to be read
   * @param string $type
   *                  The type of file (info, routing, services)
   *
   * @return array|false|mixed|string[]
   */
  public static function readComponent(string $component, string $type = 'info') {
    $core_component = __DIR__ . '/' . $component . '/extension.' . $type . '.yml';
    if (is_file($core_component)) {
      return self::fromYamlFile($core_component);
    }
    return FALSE;
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