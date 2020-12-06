<?php

namespace Nick\Menu\Pages;

use Exception;
use Nick;
use Nick\Form\Form;
use Nick\Form\FormInterface;
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
  public function setCacheOptions($parameters = []): self {
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
   * @return bool|Form
   */
  protected function defaultForm(int $id) {
    try {
      $menuObject = Menu::load($id, FALSE);
    } catch (Exception $e) {
      return FALSE;
    }
    return \Nick::Form($menuObject);
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $form = $this->defaultForm((int)$parameters[2]);
    if (!$form) {
      $result = '';
    } else {
      $result = $form->result();
    }

    if (!isset($parameters[2])) {
      return NULL;
    }
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('menu')
      ->render([
        'form' => $result,
      ]);
  }

}
