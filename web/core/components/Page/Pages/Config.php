<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Language\Language;
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

  /** @var Language $language */
  protected Language $language;

  /**
   * Config constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->formBuilder = new FormBuilder();
    $this->language = new Language();
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
      'controller' => '\\Nick\\Page\\Pages\\Config',
    ]);
  }

  protected function siteForm() {
    $siteValues = Nick::Config()->get('site');
    $languages = $this->language->getAvailableLanguages();
    $options = [];
    foreach ($languages as $langcode => $language) {
      $options[$langcode] = $language['language'] . ' [' . $langcode . ']';
    }
    return $this->formBuilder->setId('site-settings')->setFields([
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
          'attributes' => [
            'type' => 'text',
            'placeholder' => 'en-US',
          ],
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
        ],
      ],
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
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
          return Nick::Renderer()
            ->setType()
            ->setTemplate('config')
            ->render([
              'form' => $this->siteForm()->result(),
            ]);
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