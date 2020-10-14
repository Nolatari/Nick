<?php

use Nick\Language\Language;
use Nick\Cache\CacheInterface;
use Nick\Core;

/**
 * Include Settings
 */
require_once __DIR__ . '/components/Settings.php';

/** Require Nick component */
require_once 'Nick.php';

/** @var CacheInterface $cache */
$cache = Core::getCacheClass();
$cache->initializeCache();

/**
 * @param string $string
 * @param array  $args
 *
 * @return mixed
 */
function translate(string $string, $args = []) {
  /** @var \Nick\Language\Language $language */
  $language = \Nick::LanguageManager()->getCurrentLanguage();
  return $language->translate($string, $args);
}
