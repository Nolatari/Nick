<?php

namespace Nick\Cache;

/**
 * Class Redis
 *
 * @package Nick\Cache
 */
class Redis implements CacheInterface {

  /**
   * @inheritDoc
   */
  public function initializeCache() {
    // TODO: Implement initializeCache() method.
  }

  /**
   * @inheritDoc
   */
  public function getData(string $cacheKey, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []) {
    // TODO: Implement getData() method.
  }

  /**
   * @inheritDoc
   */
  public function getContentData(array $cacheOptions, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []) {
    // TODO: Implement getContentData() method.
  }

  /**
   * @inheritDoc
   */
  public function clearAllCaches() {
    // TODO: Implement clearAllCaches() method.
  }
}