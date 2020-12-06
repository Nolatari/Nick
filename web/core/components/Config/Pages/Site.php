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
 * Class Site
 *
 * @package Nick\Config\Pages
 */
class Site extends Page {

  /** @var Form $form */
  protected Form $form;

  /**
   * Config constructor.
   */
  public function __construct() {
    $this->form = new Form();
    $this->setParameters([
      'id' => 'config.site',
      'title' => $this->translate('Site settings'),
      'summary' => $this->translate('Configuration options'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'page.config.site',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * @return FormInterface
   */
  protected function siteForm() {
    return new SiteSettingsForm();
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    $form = $this->siteForm()->result();

    return \Nick::Renderer()
      ->setType()
      ->setTemplate('config')
      ->render([
        'title' => $this->translate('Site settings'),
        'form' => $form,
      ]);
  }

}