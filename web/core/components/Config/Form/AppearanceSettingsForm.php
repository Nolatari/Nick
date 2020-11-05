<?php

namespace Nick\Config\Form;

use Nick;
use Nick\Entity\EntityInterface;
use Nick\Form\Form;
use Nick\Form\FormInterface;

/**
 * Class AppearanceSettingsForm
 *
 * @package Nick\Config\Form
 */
class AppearanceSettingsForm extends Form implements FormInterface {

  /**
   * AppearanceSettingsForm constructor.
   *
   * @param EntityInterface|null $entity
   */
  public function __construct(EntityInterface $entity = NULL) {
    parent::__construct($entity);
    $themes = \Nick::Theme()->getAvailableThemes();
    $options = [];
    foreach ($themes as $theme) {
      $options[$theme] = \Nick::Theme()->getThemeInfo($theme)['name'];
    }
    return $this->setId('appearance-settings')->setFields([
      'admin' => [
        'form' => [
          'type' => 'select',
          'title' => 'Admin theme',
          'default_value' => \Nick::Config()->get('theme.admin'),
          'options' => $options,
        ],
      ],
      'front' => [
        'form' => [
          'type' => 'select',
          'title' => 'Front theme',
          'default_value' => \Nick::Config()->get('theme.front'),
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
          'handler' => [$this, 'submitForm'],
        ],
      ],
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, array $values = []) {
    d($form);
    d($values);
  }

}