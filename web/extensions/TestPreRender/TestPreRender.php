<?php

namespace Nick\TestPreRender;

use Nick\Form\FormStateInterface;

/**
 * Class TestFormAlter
 *
 * @package Nick\TestFormAlter
 */
class TestPreRender {

  /**
   * Tests the FormAlter event.
   * Defined in the TestFormAlter.yml file!
   *
   * @param array              $form
   * @param string             $form_id
   * @param FormStateInterface $formState
   */
  public function preRender(array &$form, string $form_id, FormStateInterface $formState) {
    d($form);
    d($form_id);
    d($formState);
  }

}
