<?php

/**
 * Project: Nick 1.0 alpha
 * Author: Randal Vanheede
 * Year: 2020
 */

use Symfony\Component\HttpFoundation\Request;

session_start();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../settings.php';
require_once __DIR__ . '/core/includes.php';

$request = Request::createFromGlobals();

/** Bootstrap Nick. */
\Nick::Bootstrap($request);

d(Nick\Database\Database::createFieldQuery('new_field', ['type' => 'VARCHAR', 'length' => 255]));
