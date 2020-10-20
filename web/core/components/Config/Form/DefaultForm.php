<?php

namespace Nick\Config\Form;

use Nick;
use Nick\Entity\EntityInterface;
use Nick\Form\Form;
use Nick\Form\FormInterface;
use Nick\Url;

/**
 * Class SiteSettingsForm
 *
 * @package Nick\Config\Form
 */
class DefaultForm extends Form implements FormInterface {

  /**
   * SiteSettingsForm constructor.
   *
   * @param EntityInterface|null $entity
   */
  public function __construct(EntityInterface $entity = NULL) {
    parent::__construct($entity);
    $this->setId('default-settings-form');
    $this->setFields([
      'import' => [
        'form' => [
          'type' => 'button',
          'text' => 'Import',
          'attributes' => [
            'onclick' => 'window.location.href = \'' . Url::fromRoute(Nick::Route()->load('config.import')) . '\';',
          ],
          'classes' => ['btn-success'],
        ],
      ],
      'export' => [
        'form' => [
          'type' => 'button',
          'text' => 'Export',
          'attributes' => [
            'onclick' => 'window.location.href = \'' . Url::fromRoute(Nick::Route()->load('config.export')) . '\';',
          ],
          'classes' => ['btn-success'],
        ],
      ],
      'difference' => [
        'form' => [
          'type' => 'button',
          'text' => 'Difference',
          'attributes' => [
            'onclick' => 'window.location.href = \'' . Url::fromRoute(Nick::Route()->load('config.import')) . '\';',
          ],
          'classes' => ['btn-success'],
        ],
      ],
    ]);
  }

}