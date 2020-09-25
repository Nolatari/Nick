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
      'controller' => '\\Nick\\Menu\\Pages\\Menu',
    ]);
  }

  /**
   * @param int $id
   *
   * @return Form
   */
  protected function defaultForm(int $id) {
    $menuObject = new Nick\Menu\Menu();
    try {
      $menuObject = $menuObject->loadByProperties(['id' => $id]);
    } catch (\Exception $e) {
    }
    return new Form($menuObject);
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);

    if (isset($parameters['t']) && $parameters['t'] == 'edit') {
      if (!isset($parameters['id'])) {
        return NULL;
      }
      return Nick::Renderer()
        ->setType()
        ->setTemplate('menu')
        ->render([
          'form' => $this->defaultForm((int) $parameters['id'])->result(),
        ]);
    }
    return NULL;
  }

}