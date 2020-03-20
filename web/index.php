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

include 'pages/header.php';
$page = $_GET['p'] ?? 'dashboard';
$page = 'pages/' . $page . '.php';
if (is_file($page)) {
  include $page;
}
include 'pages/footer.php';

d(\Nick::Config()->difference());