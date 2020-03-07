<?php

use Nick\Cache;
use Nick\Core;
use Nick\Database\Database;
use Nick\Logger;
use Nick\Matter\Matter;
use Nick\Matter\MatterInterface;
use Nick\Form\FormBuilder;
use Nick\Form\FormElement;
use Nick\Manifest\Manifest;

/**
 * Nick helper class.
 */
class Nick {

  /**
   * @return Cache
   */
  public static function Cache() {
    global $cache;
    return $cache ?? new Cache();
  }

  /**
   * Returns Matter object
   *
   * @return Matter
   */
  public static function matterTypeManager() {
    return new Matter();
  }

  /**
   * Returns Manifest object
   *
   * @param string $type
   *
   * @return Manifest
   */
  public static function Manifest($type) {
    return new Manifest($type);
  }

  /**
   * Returns FormBuilder object
   *
   * @param MatterInterface $matter
   *
   * @return FormBuilder
   */
  public static function FormBuilder(MatterInterface $matter) {
    return new FormBuilder($matter);
  }

  /**
   * Returns FormElement object
   *
   * @return FormElement
   */
  public static function FormElement() {
    return new FormElement();
  }

  /**
   * Returns cached Logger object
   *
   * @return Logger
   */
  public static function Logger() {
    return self::Cache()->getData('logger', '\\Nick\\Logger');
  }

  /**
   * Returns cached Database object
   *
   * @return Database
   */
  public static function Database() {
    return self::Cache()->getData('database', '\\Nick\\Database\\Database');
  }

  /**
   * Bootstraps Nick
   */
  public static function Bootstrap() {
    $core = new Core();
    $core->createMatters();
    $core->setSystemSpecifics();
  }

}