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
];

$nick_settings['config'] = [
  'folder' => __DIR__ . '/config',
];

$nick_settings['themes'] = [
  'folder' => __DIR__ . '/web/themes',
];

//$settings['debugging'] = TRUE;
//$settings['twig_debugging'] = TRUE;
//$settings['development'] = TRUE;