<?php

namespace Nick\Config\Form;

use Nick;
use Nick\Form\Form;
use Nick\Form\FormInterface;
use Nick\Matter\MatterInterface;

/**
 * Class SiteSettingsForm
 *
 * @package Nick\Config\Form
 */
class SiteSettingsForm extends Form implements FormInterface {

  /**
   * SiteSettingsForm constructor.
   *
   * @param MatterInterface|null $matter
   */
  public function __construct(MatterInterface $matter = NULL) {
    parent::__construct($matter);
    $siteValues = Nick::Config()->get('site');
    $languages = Nick::Language()->getAvailableLanguages();
    $options = [];
    foreach ($languages as $langcode => $language) {
      $options[$langcode] = '[' . $langcode . '] ' . $language['language'] . ' - ' . $language['country'];
    }
    $this->setId('site-settings');
    $this->setFields([
      'name' => [
        'form' => [
          'type' => 'textbox',
          'title' => $this->translate('Website name'),
          'default_value' => $siteValues['name'],
          'attributes' => [
            'type' => 'text',
            'placeholder' => 'My Website',
          ],
        ],
      ],
      'default-langcode' => [
        'form' => [
          'type' => 'select',
          'title' => $this->translate('Default langcode'),
          'default_value' => $siteValues['default_langcode'],
          'options' => $options,
        ],
      ],
      'submit' => [
        'form' => [
          'type' => 'button',
          'text' => $this->translate('Save settings'),
          'attributes' => [
            'type' => 'submit',
          ],
          'classes' => [
            'btn-success'
          ],
          'handler' => [$this, 'saveSiteForm'],
        ],
      ],
    ]);
  }

}