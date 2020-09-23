<?php

namespace Nick\Config\Form;

use Nick;
use Nick\Form\Form;
use Nick\Form\FormInterface;
use Nick\Matter\MatterInterface;

class AppearanceSettingsForm extends Form implements FormInterface {

  /**
   * AppearanceSettingsForm constructor.
   *
   * @param MatterInterface|null $matter
   */
  public function __construct(MatterInterface $matter = NULL) {
    parent::__construct($matter);
    $themes = Nick::Theme()->getAvailableThemes();
    $options = [];
    foreach ($themes as $theme) {
      $options[$theme] = Nick::Theme()->getThemeInfo($theme)['name'];
    }
    return $this->setId('appearance-settings')->setFields([
      'admin' => [
        'form' => [
          'type' => 'select',
          'title' => 'Admin theme',
          'default_value' => Nick::Config()->get('theme.admin'),
          'options' => $options,
        ],
      ],
      'front' => [
        'form' => [
          'type' => 'select',
          'title' => 'Front theme',
          'default_value' => Nick::Config()->get('theme.front'),
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
          'handler' => [$this, 'saveAppearanceForm'],
        ],
      ],
    ]);
  }

}