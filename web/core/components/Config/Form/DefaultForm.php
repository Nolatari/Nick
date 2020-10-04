<?php

namespace Nick\Config\Form;

use Nick;
use Nick\Form\Form;
use Nick\Form\FormInterface;
use Nick\Entity\EntityInterface;
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
            'onclick' => 'javascript:window.location.replace("' . Url::fromRoute('config', ['import' => NULL]) . '");',
          ],
          'classes' => ['btn-success'],
        ],
      ],
      'export' => [
        'form' => [
          'type' => 'button',
          'text' => 'Export',
          'attributes' => [
            'onclick' => 'javascript:window.location.replace("' . Url::fromRoute('config', ['export' => NULL]) . '");',
          ],
          'classes' => ['btn-success'],
        ],
      ],
      'difference' => [
        'form' => [
          'type' => 'button',
          'text' => 'Difference',
          'attributes' => [
            'onclick' => 'javascript:window.location.replace("' . Url::fromRoute('config', ['difference' => NULL]) . '");',
          ],
          'classes' => ['btn-success'],
        ],
      ],
    ]);
  }

}