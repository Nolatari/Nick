<?php

namespace Nick\Form\Pages;

use Nick;
use Nick\Form\FormStateInterface;
use Nick\Language\Language;
use Nick\Logger;
use Nick\Page\Page;
use Nick\Form\FormBuilder;

/**
 * Class FormState
 *
 * @package Nick\Form
 */
class FormState extends Page {

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
      'id' => 'formstate',
      'title' => $this->translate('Form State '),
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
   * @param $form
   * @param FormStateInterface $formState
   */
  protected function saveFormstate(FormStateInterface $formState) {
    $values = $_POST;
    $formState->setValues($values);
    $formState->save();
  }

}