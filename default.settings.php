<?php

$nick_settings = [];

$nick_settings['database'] = [
  'type' => 'mysql',
  'hostname' => 'hostname',
  'username' => 'username',
  'database' => 'database',
  'password' => 'password',
  'port' => 'port',
];

$nick_settings['root'] = [
  'folder' => __DIR__,
  'url' => 'http://headless_cms.lndo.site',
  'webroot' => '/web/', // With trailing slash
];

$nick_settings['config'] = [
  'folder' => __DIR__ . '/config',
];

$nick_settings['themes'] = [
  'folder' => __DIR__ . '/web/themes',
];

$nick_settings['files'] = [
  'public' => __DIR__ . '/files/public',
  'private' => __DIR__ . '/files/private',
];

//$nick_settings['debugging'] = TRUE;
//$nick_settings['twig_debugging'] = TRUE;
//$nick_settings['development'] = TRUE;