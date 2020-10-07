<?php

namespace Nick\Config\Pages;

use Nick;
use Nick\Config\Form\AppearanceSettingsForm;
use Nick\Config\Form\DefaultForm;
use Nick\Config\Form\SiteSettingsForm;
use Nick\Form\Form;
use Nick\Form\FormInterface;
use Nick\Language\Language;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

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
    $this->form = new Form();
    $this->language = new Language();
    $this->setParameters([
      'id' => 'config',
      'title' => $this->translate('Config'),
      'summary' => $this->translate('Configuration options'),
    ]);
    parent::__construct();
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
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    if (isset($parameters['export']) && isset($parameters['confirm'])) {
      Nick::Config()->export();
    } elseif (isset($parameters['import']) && isset($parameters['confirm'])) {
      Nick::Config()->import();
    } elseif (isset($paremeters['difference'])) {
      // TODO
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
          default:
            $form = $this->defaultForm()->result();
            break;
        }
      } else {
        $form = $this->defaultForm()->result();
      }

      return Nick::Renderer()
        ->setType()
        ->setTemplate('config')
        ->render([
          'title' => isset($parameters['t']) ? ucfirst($parameters['t']) . ' settings' : 'Settings',
          'form' => $form,
        ]);
    }

    return NULL;
  }

  /**
   * @return FormInterface
   */
  protected function siteForm() {
    return new SiteSettingsForm();
  }

  /**
   * @return FormInterface
   */
  protected function appearanceForm() {
    return new AppearanceSettingsForm();
  }

  /**
   * @return FormInterface
   */
  protected function defaultForm() {
    return new DefaultForm();
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

}