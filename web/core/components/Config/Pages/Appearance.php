<?php

namespace Nick\Config\Pages;

use Nick;
use Nick\Config\Form\AppearanceSettingsForm;
use Nick\Config\Form\SiteSettingsForm;
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
  public function __construct() {
    $this->form = new Form();
    $this->setParameters([
      'id' => 'config.appearance',
      'title' => $this->translate('Appearance settings'),
      'summary' => $this->translate('Configuration options'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
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
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    $form = $this->appearanceForm()->result();

    return Nick::Renderer()
      ->setType()
      ->setTemplate('config')
      ->render([
        'title' => $this->translate('Appearance settings'),
        'form' => $form,
      ]);
  }

}