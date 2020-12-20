<?php

namespace Nick\Language;

use Nick\Event\EventListener;

/**
 * Class Events
 *
 * @package Nick\Language
 */
class Events extends EventListener {

  /**
   * {@inheritDoc}
   */
  public function pagePreRender(?array &$variables, string $page_id) {
    if ($page_id !== 'header') {
      return;
    }

    $languages = \Nick::LanguageManager()->getAvailableLanguages();

    $variables['elements']['navbar']['languages'] = \Nick::Renderer()
      ->setType('core.Language')
      ->setTemplate('language-picker')
      ->render([
        'languages' => $languages,
      ]);
  }

}