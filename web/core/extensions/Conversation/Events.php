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
  public function pagePreRender(?array &$variables, string $page_id) {
    if ($page_id !== 'header') {
      return;
    }

    $variables['conversations'] = Nick::Renderer()
      ->setType('extension.Conversation')
      ->setTemplate('conversation')
      ->render($variables);
  }

}