<?php

namespace Nick\Breadcrumb;

use Nick;
use Nick\ArrayManipulation;
use Nick\Event\EventListener;
use Nick\Settings;
use Nick\StringManipulation;

/**
 * Class Breadcrumb
 *
 * @package Nick\Breadcrumb
 */
class Breadcrumb extends EventListener {

  /**
   * {@inheritDoc}
   */
  public function pagePreRender(?array &$variables, string $page_id) {
    if ($page_id !== 'header') {
      return;
    }

    $current_route = \Nick::CurrentRoute();
    $home = translate('Home');

    $items_raw = StringManipulation::explode($current_route->getUri(), '/');
    $items_raw = [$home] + ArrayManipulation::removeEmptyEntries($items_raw);
    $last_item = end($items_raw);
    $items = [];

    $url = '';
    foreach ($items_raw as $item) {
      if ($item !== $home) {
        $url = $url . '/' . $item;
      }
      $items[StringManipulation::capitalize($item)] = [
        'url' => $item === 'Home' ? Settings::get('root.web.url') . '/' : Settings::get('root.web.url') . $url,
        'link' => $item !== $last_item && !($item === 'Home' && $current_route->getRoute() === 'dashboard'),
      ];
    }

    $variables['breadcrumb'] = \Nick::Renderer()
      ->setType('extension.Breadcrumb')
      ->setTemplate('breadcrumb')
      ->render(['items' => $items]);
  }

}