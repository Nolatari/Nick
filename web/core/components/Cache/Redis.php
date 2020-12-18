<?php

namespace Nick\Cache;

use Nick\Logger;
use Nick\Settings;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;

/**
 * Class Redis
 *
 * @package Nick\Cache
 */
class Redis extends CacheBase {

  /** @var RedisTagAwareAdapter $redis */
  protected RedisTagAwareAdapter $redis;

  /**
   * Redis constructor.
   */
  public function __construct() {
    $settings = Settings::getAll();
    if (!isset($settings['redis'])) {
      \Nick::Logger()->add('No redis settings present', Logger::TYPE_ERROR, 'Redis');
    }
    $client = RedisAdapter::createConnection('redis://' . $settings['redis']['host'] . ':' . $settings['redis']['port'], $settings['redis']['config']);
    $this->redis = new RedisTagAwareAdapter($client);
    d($this->redis);exit;
  }

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
   *
   * @throws \Psr\Cache\InvalidArgumentException
   */
  public function invalidateTags(array $tags): bool {
    return $this->redis->invalidateTags($tags);
  }

  /**
   * {@inheritDoc}
   */
  public function clearAllCaches(): bool {
    // TODO: Implement clearAllCaches() method.
  }

}
