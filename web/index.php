<?php

/**
 * Project: Nick 1.0 alpha
 * Author: Randal Vanheede
 * Year: 2020
 */

session_start();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../settings.php';
require_once __DIR__ . '/core/includes.php';

$include_file = $_GET['p'] ?? 'dashboard';
$include_file = 'pages/' . $include_file . '.php';
if (is_file($include_file)) {
  include $include_file;
}

\Nick::Config()->difference();
