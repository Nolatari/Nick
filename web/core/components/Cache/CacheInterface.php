<?php

namespace Nick\Cache;

/**
 * Interface CacheInterface
 *
 * @package Nick\Cache
 */
interface CacheInterface {

  /**
   * Sets global variables in cache
   */
  public function initializeCache();

  /**
   * Checks if data is in cache, if so read it from cache.
   * If data not in cache, use fallback and add it to cache.
   * This is mainly for reusing classes/methods!
   * For storing content => self::getContentData().
   *
   * @param string $cacheKey
   * @param string $fallbackClass
   * @param string $fallbackMethod
   * @param array $methodData
   * @param array $classData
   *
   * @return mixed
   */
  public function getData($cacheKey, $fallbackClass = '', $fallbackMethod = '', $methodData = [], $classData = []);

  /**
   * Checks if data is in cache, if so read it from cache.
   * If data not in cache, use fallback and add it to cache.
   * This is mainly for (nearly) permanently storing data.
   * E.g.: content, user data, ..
   *
   * @param string $cacheKey
   * @param string $fallbackClass
   * @param string $fallbackMethod
   * @param array $methodData
   * @param array $classData
   *
   * @return mixed
   */
  public function getContentData($cacheKey, $fallbackClass = '', $fallbackMethod = '', $methodData = [], $classData = []);

  /**
   * Returns the whole cache array.
   *
   * @return array
   */
  public function returnCache();

  /**
   * Returns the whole cache array.
   *
   * @return array
   */
  public function returnCacheStats();
}