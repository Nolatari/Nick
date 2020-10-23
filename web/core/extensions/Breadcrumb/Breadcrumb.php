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
  public function pagePreRender(?array &$parameters, string $page_id) {
    if ($page_id !== 'header') {
      return;
    }

    $parameters['breadcrumb'] = Nick::Renderer()
      ->setType('core.Breadcrumb')
      ->setTemplate('breadcrumb')
      ->render($parameters);
  }

}