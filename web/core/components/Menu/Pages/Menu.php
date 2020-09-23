<?php

namespace Nick\Menu\Pages;

use Nick;
use Nick\Form\Form;
use Nick\Language\Language;
use Nick\Page\Page;

/**
 * Class Menu
 *
 * @package Nick\Page
 */
class Menu extends Page {

  /** @var Form $form */
  protected Form $form;

  /** @var Language $language */
  protected Language $language;

  /**
   * Config constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->language = new Language();
    $this->setParameters([
      'id' => 'menu',
      'title' => $this->translate('Menu'),
      'summary' => $this->translate('Menu options'),
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
      'controller' => '\\Nick\\Menu\\Pages\\Menu',
    ]);
  }

  protected function defaultForm() {
    $menuObject = new Nick\Menu\Menu();
    $form = new Form($menuObject);
    d($form);
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);

    return Nick::Renderer()
      ->setType()
      ->setTemplate('menu')
      ->render([
        'form' => $this->defaultForm()->result(),
      ]);
    }

}