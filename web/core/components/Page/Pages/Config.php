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

  /**
   * @return FormBuilder
   */
  protected function appearanceForm() {
    $appearanceValues = Nick::Config()->get('theme');
    $themes = Nick::Theme()->getAvailableThemes();
    $options = [];
    foreach ($themes as $theme) {
      $options[$theme] = Nick::Theme()->getThemeInfo($theme)['name'];
    }
    return $this->formBuilder->setId('appearance-settings')->setFields([
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
        ],
      ],
    ]);
  }

  /**
   * @return FormBuilder
   */
  protected function siteForm() {
    $siteValues = Nick::Config()->get('site');
    $languages = $this->language->getAvailableLanguages();
    $options = [];
    foreach ($languages as $langcode => $language) {
      $options[$langcode] = '[' . $langcode . '] ' . $language['language'] . ' - ' . $language['country'];
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

  protected function defaultForm() {
    return $this->formBuilder->setId('default-settings-form')->setFields([
      'import' => [
        'form' => [
          'type' => 'button',
          'text' => 'Import',
          'attributes' => [
            'onclick' => 'javascript:window.location.replace("./?p=config&import");',
          ],
          'classes' => ['btn-success'],
        ],
      ],
      'export' => [
        'form' => [
          'type' => 'button',
          'text' => 'Export',
          'attributes' => [
            'onclick' => 'javascript:window.location.replace("./?p=config&export");',
          ],
          'classes' => ['btn-success'],
        ],
      ],
      'difference' => [
        'form' => [
          'type' => 'button',
          'text' => 'Difference',
          'attributes' => [
            'onclick' => 'javascript:window.location.replace("./?p=config&difference");',
          ],
          'classes' => ['btn-success'],
        ],
      ],
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);
    if (isset($parameters['export']) && isset($parameters['confirm'])) {
      Nick::Config()->export();
    } elseif (isset($parameters['import']) && isset($parameters['confirm'])) {
      Nick::Config()->import();
    } else {
      $form = NULL;
      if (isset($parameters['t'])) {
        switch ($parameters['t']) {
          case 'site':
            $form = $this->siteForm()->result();
            break;
          case 'appearance':
            $form = $this->appearanceForm()->result();
            break;
          case 'default':
            $form = $this->defaultForm()->result();
            break;
        }
      }

      return Nick::Renderer()
        ->setType()
        ->setTemplate('config')
        ->render([
          'form' => $form,
        ]);
    }

    return NULL;
  }

}