<?php

namespace Nick\Cache;

use Nick;
use Nick\Database\Result;
use Nick\Event\Event;
use Nick\Logger;

/**
 * Class Cache => used to store cache in database.
 *
 * @package Nick\Cache
 */
class CacheBase implements CacheInterface {

  /** @var array $cacheableData */
  protected array $cacheableData;

  /** @var array $cacheStats */
  protected array $cacheStats;

  /**
   * {@inheritDoc}
   */
  public function getData(string $cacheKey, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []) {
    if (!isset($this->cacheStats[$cacheKey])) {
      $this->cacheStats[$cacheKey]['created'] = 0;
      $this->cacheStats[$cacheKey]['called'] = 0;
    }
    if (!isset($this->cacheableData[$cacheKey])) {
      $class = new $fallbackClass(...$classData);
      if (!empty($fallbackMethod)) {
        $this->cacheableData[$cacheKey] = $class->{$fallbackMethod}(...$methodData);
      } else {
        $this->cacheableData[$cacheKey] = $class;
      }
      $this->cacheStats[$cacheKey]['created']++;
    }
    $this->cacheStats[$cacheKey]['called']++;
    return $this->cacheableData[$cacheKey];
  }

  /**
   * {@inheritDoc}
   */
  public function getContentData(array $cacheOptions, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []) {
    // Method stub
    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function invalidateTags(array $tags): bool {
    // Method stub
    return TRUE;
  }

  /**
   * @param array $cacheOptions
   * @param mixed $value
   *
   * @return bool
   */
  protected function insertContentData(array $cacheOptions, $value = []) {
    // Method stub
    return TRUE;
  }

  /**
   * @param array $cacheOptions
   * @param mixed $value
   *
   * @return bool
   */
  protected function updateContentData(array $cacheOptions, $value = []) {
    // Method stub
    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function clearAllCaches(): bool {
    // Method stub
    return TRUE;
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

  /**
   * {@inheritDoc}
   */
  public function initializeCache() {
    $this->cacheableData['NICK_VERSION'] = '1';
    $this->cacheableData['NICK_VERSION_RELEASE'] = '0';
    $this->cacheableData['NICK_VERSION_RELEASE_MINOR'] = '0';
    $this->cacheableData['NICK_VERSION_STATUS'] = 'alpha';
  }
}