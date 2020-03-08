<?php

if (!isset($_GET['type'])
  || !isset($_GET['keyword'])
  || !isset($_GET['field'])) {
  die;
}

$items = Nick::Manifest($_GET['type'])
  ->fields(['id', $_GET['field']])
  ->condition($_GET['field'], $_GET['keyword'], 'LIKE')
  ->result();

$json_array = [];
foreach ($items as $item) {
  $json_array[] = [
    'value' => (int)$item['id'],
    'text' => $item[$_GET['field']],
  ];
}

header('Content-type: text/javascript');
echo json_encode($json_array);