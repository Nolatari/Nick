<?php

namespace Nick\Config\Pages;

use Nick;
use Nick\Config\Form\AppearanceSettingsForm;
use Nick\Form\Form;
use Nick\Form\FormInterface;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Appearance
 *
 * @package Nick\Config\Pages
 */
class Appearance extends Page {

  /** @var Form $form */
  protected Form $form;

  /**
   * Config constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->form = new Form();
    $this->setParameters([
      'id' => 'config.appearance',
      'title' => $this->translate('Appearance settings'),
      'summary' => $this->translate('Configuration options'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'page.config.appearance',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * @return FormInterface
   */
  protected function appearanceForm() {
    return new AppearanceSettingsForm();
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();
    $form = $this->appearanceForm()->result();

    return \Nick::Renderer()
      ->setType()
      ->setTemplate('config')
      ->render([
        'title' => $this->translate('Appearance settings'),
        'form' => $form,
      ]);
  }

}