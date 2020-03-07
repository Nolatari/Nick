<?php

use Nick\Cache;

/**
 * Include Settings
 */
require_once __DIR__ . '/components/Settings.php';

/** @var Cache $cache */
$cache = new Cache();
$cache->initializeCache();

/** Require Nick component */
require_once 'Nick.php';

/** Bootstrap Nick. */
\Nick::Bootstrap();