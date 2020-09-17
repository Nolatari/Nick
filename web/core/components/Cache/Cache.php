<?php

namespace Nick\Cache;

use Nick;
use Nick\Event\Event;
use Nick\Database\Result;
use Nick\Logger;
use Nick\Settings;

/**
 * Class Cache => used to store cache in database.
 *
 * @package Nick\Cache
 */
class Cache extends Settings implements CacheInterface {

  /** @var array $cacheableData */
  protected array $cacheableData;

  /** @var array $cacheStats */
  protected array $cacheStats;

  /**
   * {@inheritDoc}
   */
  public function initializeCache() {
    $this->cacheableData['NICK_VERSION'] = '0';
    $this->cacheableData['NICK_VERSION_RELEASE'] = '8';
    $this->cacheableData['NICK_VERSION_RELEASE_MINOR'] = '1';
    $this->cacheableData['NICK_VERSION_STATUS'] = 'beta';
  }

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
    // Fires an event to alter cache options before being sent to DB.
    $event = new Event('cacheContentAlter');
    $event->fire($cacheOptions);

    $query = Nick::Database()
      ->select('cache_content')
      ->condition('field', $cacheOptions['key'])
      ->execute();
    if (!$query instanceof Result) {
      return FALSE;
    }

    $items = $query->fetchAllAssoc();
    if (count($items) == 0) {
      $class = new $fallbackClass(...$classData);
      if (empty($fallbackMethod)) {
        return FALSE;
      }
      $data = $class->{$fallbackMethod}(...$methodData);
      if (!$this->insertContentData($cacheOptions, $data)) {
        return FALSE;
      }
    } else {
      $cache = $items;
      $cache = reset($cache);
      $class = new $fallbackClass(...$classData);
      if (empty($fallbackMethod)) {
        return FALSE;
      }
      if ((time() - $cache['created']) > $cache['maxage'] && $cache['maxage'] != '-1') {
        $data = $class->{$fallbackMethod}(...$methodData);
        if (!$this->updateContentData($cacheOptions, $data)) {
          return FALSE;
        }
      } else {
        $data = unserialize($cache['value']);
      }
    }

    // Add to cache stats.
    if (!isset($this->cacheStats['content'][$cacheOptions['key']])) {
      $this->cacheStats['content'][$cacheOptions['key']]['created'] = 1;
      $this->cacheStats['content'][$cacheOptions['key']]['called'] = 0;
    }
    $this->cacheStats['content'][$cacheOptions['key']]['called']++;

    return $data;
  }

  /**
   * @param array $cacheOptions
   * @param mixed $value
   *
   * @return bool
   */
  protected function insertContentData(array $cacheOptions, $value = []) {
    $query = Nick::Database()
      ->insert('cache_content')
      ->values([
        'field' => $cacheOptions['key'],
        'value' => serialize($value),
        'tags' => $cacheOptions['tags'] ?? '',
        'context' => $cacheOptions['context'] ?? '',
        'maxage' => $cacheOptions['max-age'] ?? 0,
        'created' => time(),
      ])
      ->execute();
    if (!$query) {
      Nick::Logger()->add('Something went wrong trying to insert cache item [' . $cacheOptions['key'] . ']', Logger::TYPE_FAILURE, 'Cache');
      return FALSE;
    }

    return TRUE;
  }

  /**
   * @param array $cacheOptions
   * @param mixed $value
   *
   * @return bool
   */
  protected function updateContentData(array $cacheOptions, $value = []) {
    $query = Nick::Database()
      ->update('cache_content')
      ->condition('field', $cacheOptions['key'])
      ->values([
        'value' => serialize($value),
        'tags' => $cacheOptions['tags'] ?? '',
        'context' => $cacheOptions['context'] ?? '',
        'maxage' => $cacheOptions['max-age'] ?? 0,
        'created' => time(),
      ])
      ->execute();
    if (!$query) {
      Nick::Logger()->add('Something went wrong trying to update cache item [' . $cacheOptions['key'] . ']', Logger::TYPE_FAILURE, 'Cache');
      return FALSE;
    }

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
  public function clearAllCaches() {
    $this->cacheableData = [];
    $this->initializeCache();
    return Nick::Database()->query('TRUNCATE TABLE cache_content');
  }

}