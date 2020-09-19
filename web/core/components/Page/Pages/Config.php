<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Form\FormBuilder;

/**
 * Class Config
 *
 * @package Nick\Page
 */
class Config extends Page {

  /** @var FormBuilder $formBuilder */
  protected FormBuilder $formBuilder;

  /**
   * Config constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->formBuilder = new FormBuilder();
    $this->setParameters([
      'title' => $this->translate('Config'),
      'summary' => $this->translate('Configuration options'),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.config',
      'context' => 'page',
      'max-age' => 300,
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
      'controller' => '\\Nick\\Page\\Pages\\Config',
    ]);
  }

  protected function siteForm() {
    $siteValues = Nick::Config()->get('site');
    $form = $this->formBuilder->setId('site-settings')->setFields([
      'title' => [
        'type' => 'varchar',
        'length' => 255,
        'unique' => TRUE,
        'form' => [
          'type' => 'textbox',
          'default_value' => $siteValues['name'],
          'attributes' => [
            'type' => 'text',
          ],
        ],
      ],
      'default_langcode' => [
        'type' => 'varchar',
        'length' => 255,
        'unique' => TRUE,
        'form' => [
          'type' => 'textbox',
          'default_value' => $siteValues['default_langcode'],
          'attributes' => [
            'type' => 'text',
          ],
        ],
      ],
    ]);
    d($form->result());
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    $this->siteForm();
    parent::render($parameters);
    if (isset($parameters['export'])) {
      Nick::Config()->export();
    } elseif (isset($parameters['import'])) {
      Nick::Config()->import();
    } elseif (isset($parameters['difference'])) {
      // @TODO
    } else {
      switch ($parameters['t']) {
        case 'site':
          // @TODO
          break;
        case 'appearance':
          // @TODO
          break;
        default:
          break;
      }
    }
  }

}