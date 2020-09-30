<?php

namespace Nick;

use Exception;
use Nick;
use Nick\Cache\Cache;
use Nick\Cache\CacheInterface;
use Nick\Database\Result;
use Nick\Entity\EntityInterface;

/**
 * Class Core
 *
 * @package Nick
 */
class Core {

  /**
   * Sets Nick's exception handler.
   */
  public function setSystemSpecifics() {
    @set_exception_handler([$this, 'Exception']);
  }

  /**
   * Logs exceptions through Nick's Logger class.
   *
   * @param Exception $exception
   */
  public function Exception($exception) {
    d($exception);
    d($exception->getTraceAsString());
    Nick::Logger()->add($exception->getMessage(), Logger::TYPE_ERROR, 'Exception', $exception->getTraceAsString());
  }

  /**
   * Generates random uuid.
   *
   * @return string
   */
  public static function createUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000,
      mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }

  /**
   * Returns currently active Cache class.
   *
   * @return CacheInterface
   */
  public static function getCacheClass() {
    // @TODO: dynamically return the currently active cache class!
    $cacheClass = new Cache;
    if (!$cacheClass instanceof CacheInterface) {
      // Return default Cache class in case the custom one is not an instance of Cacheinterface.
      return new Cache;
    }
    return $cacheClass;
  }

}
