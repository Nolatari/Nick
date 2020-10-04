<?php

namespace Nick\Conversation;

use Nick;

/**
 * Class Events
 *
 * @package Nick\Conversation
 */
class Events {

  /**
   * Add conversation dropdown to header.
   *
   * @param array|null $parameters
   * @param string     $page_id
   */
  public function prePageRender(?array &$parameters, string $page_id) {
    if ($page_id !== 'header') {
      return;
    }

    $parameters['conversations'] = Nick::Renderer()
      ->setType('core.Conversation')
      ->setTemplate('conversation')
      ->render($parameters);
  }

}