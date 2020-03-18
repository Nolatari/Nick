<?php

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

/** Bootstrap Nick. */
\Nick::Bootstrap();