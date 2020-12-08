<?php

$nick_settings = [];

// Default subsite
$nick_settings['default'] = [
  'url' => 'localhost',
  'database' => [
    'type' => 'mysql',
    'hostname' => 'hostname',
    'username' => 'username',
    'database' => 'database',
    'password' => 'password',
    'port' => '3306',
  ],
  'root' => [
    'folder' => __DIR__,
    'web' => [
      'url' => 'http://localhost',
      'root' => '/',
    ],
  ],
  'config' => [
    'folder' => __DIR__ . '/config',
  ],
  'themes' => [
    'folder' => 'themes',
    'url' => $nick_settings['root']['web']['url'] . '/themes',
  ],
  'files' => [
    'public' => $nick_settings['root']['web']['url'] . '/files/default/public',
    'private' => $nick_settings['root']['web']['url'] . '/files/default/private',
  ],
  'debugging' => FALSE,
  'twig_debugging' => FALSE,
  'development' => FALSE,
];
