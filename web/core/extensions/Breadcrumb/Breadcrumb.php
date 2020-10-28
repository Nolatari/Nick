<?php

namespace Nick\Breadcrumb;

use Nick;
use Nick\Event\EventListener;

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

    $variables['breadcrumb'] = \Nick::Renderer()
      ->setType('extension.Breadcrumb')
      ->setTemplate('breadcrumb')
      ->render($variables);
  }

}