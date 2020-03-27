<?php

$settings = [];

$settings['database'] = [
  'type' => 'mysql',
  'hostname' => 'hostname',
  'username' => 'username',
  'database' => 'database',
  'password' => 'password',
  'port' => 'port',
];

$settings['root'] = [
  'folder' => __DIR__,
  'url' => 'http://headless_cms.lndo.site',
];

$settings['config'] = [
  'folder' => __DIR__ . '/config',
];

//$settings['debugging'] = TRUE;
//$settings['twig_debugging'] = TRUE;