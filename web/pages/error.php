<?php

switch ($_GET['e']) {
  case '404':
    $title = 'Page not found';
    break;
  case '403':
    $title = 'Forbidden';
    break;
  case '301':
    $title = 'Moved permanently';
    break;
  default:
    $_GET['e'] = '500';
    $title = 'Internal server error';
    break;
}

$variables['page']['title'] = $title;
$page = \Nick::Renderer()->setType('error')->setTemplate($_GET['e'])->render();