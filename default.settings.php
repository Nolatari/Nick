<?php

$nick_settings = [];

// ---------------------------------------- //
//         Start Default subsite            //
// ---------------------------------------- //
$nick_settings['default'] = [];
$nick_settings['default']['url'] = 'localhost';
$nick_settings['default']['cache_backend'] = '\Nick\Cache\Cache';
/**
 * Example of REDIS cache backend:
 *
 * $nick_settings['default']['cache_backend'] = '\Nick\Cache\Redis';
 * $nick_settings['default']['redis'] = [
 *   'host' => 'localhost',
 *   'port' => '6379',
 *   'config' => [
 *     'compression' => true,
 *     'lazy' => false,
 *     'persistent' => 0,
 *     'persistent_id' => null,
 *     'tcp_keepalive' => 0,
 *     'timeout' => 30,
 *     'read_timeout' => 0,
 *     'retry_interval' => 0,
 *   ],
 * ];
 */
$nick_settings['default']['database'] = [
  'type' => 'mysql',
  'hostname' => 'hostname',
  'username' => 'username',
  'database' => 'database',
  'password' => 'password',
  'port' => '3306',
];
$nick_settings['default']['root'] = [
  'folder' => __DIR__,
  'web' => [
    'url' => 'http://localhost',
    'root' => '/',
  ],
];
$nick_settings['default']['config'] = [
  'folder' => __DIR__ . '/config',
];
$nick_settings['default']['themes'] = [
  'folder' => __DIR__ . '/web/themes',
  'url' => $nick_settings['default']['root']['web']['url'] . '/themes',
];
$nick_settings['default']['files'] = [
  'public' => $nick_settings['default']['root']['web']['url'] . '/files/default/public',
  'private' => $nick_settings['default']['root']['web']['url'] . '/files/default/private',
];
$nick_settings['default']['debugging'] = FALSE;
$nick_settings['default']['twig_debugging'] = FALSE;
$nick_settings['default']['development'] = FALSE;
// ---------------------------------------- //
//           End Default subsite            //
// ---------------------------------------- //