<?php

namespace Nick\Menu\Pages;

use Exception;
use Nick;
use Nick\Form\Form;
use Nick\Menu\Menu;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Menu
 *
 * @package Nick\Page
 */
class Edit extends Page {

  /** @var Form $form */
  protected Form $form;

  /**
   * Config constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'menu',
      'title' => $this->translate('Menu'),
      'summary' => $this->translate('Menu options'),
    ]);
    parent::__construct();
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
   * @param int $id
   *
   * @return Form
   */
  protected function defaultForm(int $id) {
    $menuObject = new Menu();
    try {
      $menuObject = $menuObject->load($id, FALSE);
    } catch (Exception $e) {
    }
    return new Form($menuObject);
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    if (!isset($parameters[2])) {
      return NULL;
    }
    return Nick::Renderer()
      ->setType()
      ->setTemplate('menu')
      ->render([
        'form' => $this->defaultForm((int)$parameters[2])->result(),
      ]);
  }

}
