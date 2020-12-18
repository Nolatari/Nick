<?php

namespace Nick\Cache;

/**
 * Class Redis
 *
 * @package Nick\Cache
 */
class Redis extends CacheBase {

  /**
   * {@inheritDoc}
   */
  public function getContentData(array $cacheOptions, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []) {
    // TODO: Implement getContentData() method.
  }

  /**
   * {@inheritDoc}
   */
  public function updateContentData(array $cacheOptions, $value = []) {
    // TODO: Implement updateContentData() method.
  }

  /**
   * {@inheritDoc}
   */
  public function insertContentData(array $cacheOptions, $value = []) {
    // TODO: Implement insertContentData() method.
  }

  /**
   * {@inheritDoc}
   */
  public function invalidateTags(array $tags): bool {
    // TODO: Implement invalidateTags() method.
  }

  /**
   * {@inheritDoc}
   */
  public function clearAllCaches(): bool {
    // TODO: Implement clearAllCaches() method.
  }

}
