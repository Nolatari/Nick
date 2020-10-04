<?php

namespace Nick\Conversation;

use Nick;
use Nick\Event\EventListener;

/**
 * Class Events
 *
 * @package Nick\Conversation
 */
class Events extends EventListener {

  /**
   * {@inheritDoc}
   */
  public function pagePreRender(?array &$parameters, string $page_id) {
    if ($page_id !== 'header') {
      return;
    }

    $parameters['conversations'] = Nick::Renderer()
      ->setType('core.Conversation')
      ->setTemplate('conversation')
      ->render($parameters);
  }

}