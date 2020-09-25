<?php

namespace Nick\Config\Pages;

use Nick;
use Nick\Config\Form\AppearanceSettingsForm;
use Nick\Config\Form\SiteSettingsForm;
use Nick\Form\Form;
use Nick\Language\Language;
use Nick\Page\Page;

/**
 * Class Config
 *
 * @package Nick\Page
 */
class Config extends Page {

  /** @var Form $form */
  protected Form $form;

  /** @var Language $language */
  protected Language $language;

  /**
   * Config constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->form = new Form();
    $this->language = new Language();
    $this->setParameters([
      'id' => 'config',
      'title' => $this->translate('Config'),
      'summary' => $this->translate('Configuration options'),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
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
      'controller' => '\\Nick\\Page\\Pages\\Config',
    ]);
  }

  /**
   * @return Form
   */
  protected function appearanceForm() {
    return new AppearanceSettingsForm();
  }

  /**
   * @return Form
   */
  protected function siteForm() {
    return new SiteSettingsForm();
  }

  public function saveSiteForm(&$form, &$values) {
    d('hey');
  }

  protected function defaultForm() {
    return $this->form->setId('default-settings-form')->setFields([
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