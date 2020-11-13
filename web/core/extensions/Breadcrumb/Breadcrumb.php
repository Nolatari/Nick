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

    $items_raw = StringManipulation::explode(\Nick::CurrentRoute()->getUri(), '/');
    $items_raw = ArrayManipulation::removeEmptyEntries($items_raw);
    $items = [];

    $url = '';
    foreach ($items_raw as $item) {
      $url = $url . '/' . $item;
      $items[StringManipulation::capitalize($item)] = Settings::get('root.web.url') . $url;
    }

    $variables['breadcrumb'] = \Nick::Renderer()
      ->setType('extension.Breadcrumb')
      ->setTemplate('breadcrumb')
      ->render(['items' => $items]);
  }

}