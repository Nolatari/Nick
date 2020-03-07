<?php

/**
 * Project: HomeDash 1.0 alpha
 * Author: Randal Vanheede
 * Date: 06-03-2020
 * License: MIT
 */

use Composer\Autoload\ClassLoader;

session_start();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../settings.php';
require_once __DIR__ . '/core/includes.php';

$classes = array_filter(get_declared_classes(), function ($class) {
  return strpos($class, 'Nick') !== FALSE ? $class : FALSE;
});

//d($classes);
