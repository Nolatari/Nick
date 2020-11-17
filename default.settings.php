<?php

$nick_settings = [];

$nick_settings['database'] = [
  'type' => 'mysql',
  'hostname' => 'localhost',
  'username' => 'nick-db',
  'database' => 'nick-db',
  'password' => 'nick-db',
  'port' => '3306',
];

$nick_settings['root'] = [
  'folder' => __DIR__,
  'web' => [
    'url' => 'http://localhost/web',
    'root' => '/web/',
  ],
  'project' => [
    'url' => 'http://localhost/',
    'root' => '/',
  ],
];

$nick_settings['config'] = [
  'folder' => __DIR__ . '/config',
];

$nick_settings['themes'] = [
  'folder' => 'themes',
  'url' => $nick_settings['root']['web']['url'] . '/themes',
];

$nick_settings['files'] = [
  'public' => $nick_settings['root']['project']['url'] . '/files/public',
  'private' => $nick_settings['root']['project']['url'] . '/files/private',
];

//$nick_settings['debugging'] = TRUE;
//$nick_settings['twig_debugging'] = TRUE;
//$nick_settings['development'] = TRUE;