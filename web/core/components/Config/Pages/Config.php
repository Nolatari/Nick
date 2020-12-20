<?php

namespace Nick\Config\Pages;

use Nick;
use Nick\Config\Form\ConfigForm;
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
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->form = new Form();
    $this->language = new Language();
    $this->setParameters([
      'id' => 'config',
      'title' => $this->translate('Config'),
      'summary' => $this->translate('Configuration options'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    return \Nick::PageManager()->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Page\\Pages\\Config',
    ]);
  }

  /**
   * @return FormInterface
   */
  protected function configForm() {
    return new ConfigForm();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
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
  public function render() {
    parent::render();
    if (isset($parameters['export']) && isset($parameters['confirm'])) {
      \Nick::Config()->export();
    } elseif (isset($parameters['import']) && isset($parameters['confirm'])) {
      \Nick::Config()->import();
    } elseif (isset($paremeters['difference'])) {
      // TODO
    } else {
      $form = $this->configForm()->result();

      return \Nick::Renderer()
        ->setType()
        ->setTemplate('config')
        ->render([
          'title' => isset($parameters['t']) ? ucfirst($parameters['t']) . ' settings' : 'Settings',
          'form' => $form,
        ]);
    }

    return NULL;
  }

}