<?php

namespace Nick\extension\TestFormAlter;

use Nick\Form\FormStateInterface;

/**
 * Class TestFormAlter
 * @package Nick\extension\TestFormAlter
 */
class TestFormAlter {

  /**
   * @param array $form
   * @param string $form_id
   * @param FormStateInterface $formState
   */
  public function FormAlter(array &$form, $form_id, FormStateInterface $formState) {
    d($form);
    d($form_id);
    d($formState);
  }

}