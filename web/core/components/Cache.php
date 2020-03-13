<?php

namespace Nick;

/**
 * Class Cache
 *
 * @package FileManager
 */
class Cache extends Settings {

  /** @var array $cacheableData */
  protected $cacheableData;

  /** @var array $cacheStats */
  protected $cacheStats;

  /**
   * Sets global variables in cache
   */
  public function initializeCache() {
    $this->cacheableData['NICK_VERSION'] = '1';
    $this->cacheableData['NICK_VERSION_RELEASE'] = '0';
    $this->cacheableData['NICK_VERSION_STATUS'] = 'alpha';
  }

  /**
   * Checks if data is in cache, if so read it from cache.
   * If data not in cache, use fallback and add it to cache.
   *
   * @param string $cacheKey
   * @param string $fallbackClass
   * @param string $fallbackMethod
   * @param array $methodData
   * @param array $classData
   *
   * @return mixed
   */
  public function getData($cacheKey, $fallbackClass = '', $fallbackMethod = '', $methodData = [], $classData = []) {
    if (!isset($this->cacheableData[$cacheKey])) {
      $class = new $fallbackClass(...$classData);
      if (!empty($fallbackMethod)) {
        $this->cacheableData[$cacheKey] = $class->{$fallbackMethod}(...$methodData);
      } else {
        $this->cacheableData[$cacheKey] = $class;
      }
      if (!isset($this->cacheStats[$cacheKey])) {
        $this->cacheStats[$cacheKey]['created'] = 0;
        $this->cacheStats[$cacheKey]['called'] = 0;
      }
      $this->cacheStats[$cacheKey]['created']++;
    }
    $this->cacheStats[$cacheKey]['called']++;
    return $this->cacheableData[$cacheKey];
  }

  public function getContentData($cacheKey, $fallbackClass = '', $fallbackMethod = '', $methodData = [], $classData = []) {

  }

  /**
   * Returns the whole cache array.
   *
   * @return array
   */
  public function returnCache() {
    return $this->cacheableData;
  }

  /**
   * Returns the whole cache array.
   *
   * @return array
   */
  public function returnCacheStats() {
    return $this->cacheStats;
  }

}