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
  public function getData($cacheKey, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []);

  /**
   * Checks if data is in cache, if so read it from cache.
   * If data not in cache, use fallback and add it to cache.
   * This is mainly for (nearly) permanently storing data.
   * E.g.: content, user data, ..
   *
   * @param array $cacheOptions
   * @param string $fallbackClass
   * @param string $fallbackMethod
   * @param array $methodData
   * @param array $classData
   *
   * @return mixed
   */
  public function getContentData(array $cacheOptions, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []);

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

  /**
   * Truncates caching tables
   *
   * @return bool
   */
  public function clearAllCaches();
}