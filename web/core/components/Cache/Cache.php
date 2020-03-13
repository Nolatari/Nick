<?php

namespace Nick\Cache;

use Nick\Settings;

/**
 * Class Cache
 *
 * @package Nick\Cache
 */
class Cache extends Settings implements CacheInterface {

  /** @var array $cacheableData */
  protected $cacheableData;

  /** @var array $cacheStats */
  protected $cacheStats;

  /**
   * {@inheritDoc}
   */
  public function initializeCache() {
    $this->cacheableData['NICK_VERSION'] = '1';
    $this->cacheableData['NICK_VERSION_RELEASE'] = '0';
    $this->cacheableData['NICK_VERSION_STATUS'] = 'alpha';
  }

  /**
   * {@inheritDoc}
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

  /**
   * {@inheritDoc}
   */
  public function getContentData($cacheKey, $fallbackClass = '', $fallbackMethod = '', $methodData = [], $classData = []) {
    // @TODO!
  }

  /**
   * {@inheritDoc}
   */
  public function returnCache() {
    return $this->cacheableData;
  }

  /**
   * {@inheritDoc}
   */
  public function returnCacheStats() {
    return $this->cacheStats;
  }

}