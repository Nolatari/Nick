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
   * This is mainly for the reuse of classes/methods!
   * For storing content => CacheInterface::getContentData().
   *
   * @param string $cacheKey
   *                  The key under which the data will be stored
   * @param string $fallbackClass
   *                  The fallback class to use when there's no stored data yet
   * @param string $fallbackMethod
   *                  The fallback method to use when there's no stored data yet
   * @param array  $methodData
   *                  The necessary parameters for the given method in array format
   * @param array  $classData
   *                  The necessary parameters for the given class in array format
   *
   * @return mixed
   */
  public function getData(string $cacheKey, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []);

  /**
   * Checks if data is in cache, if so read it from cache.
   * If data not in cache, use fallback and add it to cache.
   * This is mainly for (nearly) permanently storing content data.
   * E.g.: content, user data, ..
   *
   * @param array  $cacheOptions
   *                  The options for the content data (key, max-age, ..)
   * @param string $fallbackClass
   *                  The fallback class to use when there's no stored data yet
   * @param string $fallbackMethod
   *                  The fallback method to use when there's no stored data yet
   * @param array  $methodData
   *                  The necessary parameters for the given method in array format
   * @param array  $classData
   *                  The necessary parameters for the given class in array format
   *
   * @return mixed
   */
  public function getContentData(array $cacheOptions, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []);

  /**
   * Truncates caching table(s)
   *
   * @return bool
   */
  public function clearAllCaches(): bool;

  /**
   * Invalidates tags
   *
   * @param array $tags
   *
   * @return bool
   */
  public function invalidateTags(array $tags): bool;

}
