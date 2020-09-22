<?php

namespace Nick\Form\Pages;

use Nick;
use Nick\Form\FormStateInterface;
use Nick\Language\Language;
use Nick\Logger;
use Nick\Page\Page;
use Nick\Form\FormBuilder;

/**
 * Class FormSubmit
 *
 * @package Nick\Form
 */
class FormSubmit extends Page {

  /** @var FormBuilder $formBuilder */
  protected FormBuilder $formBuilder;

  /** @var Language $language */
  protected Language $language;

  /**
   * FormSubmit constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->formBuilder = new FormBuilder();
    $this->language = new Language();
    $this->setParameters([
      'id' => 'submitform',
      'title' => $this->translate('Submit Form'),
      'summary' => $this->translate('Submit form handler page.'),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.' . $this->get('id'),
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Form\\Pages\\FormSubmit',
    ]);
  }

  /**
   * @param array $form
   * @param FormStateInterface $formState
   *
   * @return bool
   */
  protected function submitForm(array $form, FormStateInterface $formState) {
    if (!$handler = $form['submit']['form']['handler']) {
      return FALSE;
    }
    $handlerClass = new $handler[0];

    try {
      // Attempt to call the submit handler
      $handlerClass->{$handler[1]}($form, $formState);
    } catch(\Exception $e) {
      Nick::Logger()->add($e->getMessage(), Logger::TYPE_ERROR, 'Form Submit');
    }

    return TRUE;
  }

  /**
   * @param array $parameters
   *
   * @return NULL|string|void
   */
  public function render($parameters = []) {
    $formState = new FormState($parameters['uuid'] ?? NULL);
    //$this->submitForm();
    return NULL;
  }

}