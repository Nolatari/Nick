<?php

namespace Nick\Cache;

use Nick;
use Nick\ArrayManipulation;
use Nick\Database\Result;
use Nick\Logger;

/**
 * Class Cache => used to store cache in database.
 *
 * @package Nick\Cache
 */
class Cache extends CacheBase {

  /**
   * {@inheritDoc}
   */
  public function getContentData(array $cacheOptions, $fallbackClass = '', $fallbackMethod = '', array $methodData = [], array $classData = []) {
    // Fires an event to alter cache options before being sent to DB.
    \Nick::Event('cacheContentAlter')
      ->fire($cacheOptions);

    // Don't cache content if max-age is 0, clearing unneeded queries.
    if (isset($cacheOptions['max-age']) && $cacheOptions['max-age'] == 0) {
      $class = new $fallbackClass(...$classData);
      if (!$class) {
        return FALSE;
      }
      return $class->{$fallbackMethod}(...$methodData);
    }

    $query = \Nick::Database()
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
    $query = \Nick::Database()
      ->insert('cache_content')
      ->values([
        'field' => $cacheOptions['key'],
        'value' => serialize($value),
        'tags' => serialize($cacheOptions['tags'] ?? []),
        'context' => $cacheOptions['context'] ?? '',
        'maxage' => $cacheOptions['max-age'] ?? 0,
        'created' => time(),
      ])
      ->execute();
    if (!$query) {
      \Nick::Logger()->add('Something went wrong trying to insert cache item [' . $cacheOptions['key'] . ']', Logger::TYPE_FAILURE, 'Cache');
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
    $query = \Nick::Database()
      ->update('cache_content')
      ->condition('field', $cacheOptions['key'])
      ->values([
        'value' => serialize($value),
        'tags' => serialize($cacheOptions['tags']) ?? '',
        'context' => $cacheOptions['context'] ?? '',
        'maxage' => $cacheOptions['max-age'] ?? 0,
        'created' => time(),
      ])
      ->execute();
    if (!$query) {
      \Nick::Logger()->add('Something went wrong trying to update cache item [' . $cacheOptions['key'] . ']', Logger::TYPE_FAILURE, 'Cache');
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function clearAllCaches(): bool {
    $this->cacheableData = [];
    $this->initializeCache();

    $caches = [];
    $caches[] = \Nick::Database()->query('TRUNCATE TABLE cache_content');
    $caches[] = \Nick::Database()->query('TRUNCATE TABLE routes');

    foreach ($caches as $cache) {
      if (!$cache) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function invalidateTags(array $tags): bool {
    $cache_storage = \Nick::Database()
      ->select('cache_content')
      ->fields(NULL, ['field', 'tags'])
      ->execute();
    if (!$cache_storage instanceof Result) {
      return FALSE;
    }

    $cache = $cache_storage->fetchAllAssoc();
    if (!$cache || count($cache) === 0) {
      return FALSE;
    }

    foreach ($cache as $item) {
      $cur_tags = unserialize($item['tags']);
      if (!isset($cur_tags) || !is_array($cur_tags)) {
        continue;
      }

      foreach ($cur_tags as $tag) {
        if (!ArrayManipulation::contains($tags, $tag)) {
          continue;
        }

        $deletion = \Nick::Database()
          ->delete('cache_content')
          ->condition('field', $item['field'])
          ->execute();

        if (!$deletion) {
          return FALSE;
        }
      }
    }

    return TRUE;
  }

}