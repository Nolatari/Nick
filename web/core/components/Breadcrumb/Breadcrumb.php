<?php

namespace Nick\Breadcrumb;

use Nick;

/**
 * Class Breadcrumb
 *
 * @package Nick\Breadcrumb
 */
class Breadcrumb {

  /**
   * Add breadcrumbs to page render
   *
   * @param array|null $parameters
   */
  public function pagePreRender(&$parameters, $page_id) {
    if ($page_id !== 'header') {
      return;
    }

    $parameters['breadcrumb'] = Nick::Renderer()
      ->setType('core.Breadcrumb')
      ->setTemplate('breadcrumb')
      ->render($parameters);
  }

}