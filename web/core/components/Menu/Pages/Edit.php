<?php

namespace Nick\Menu\Pages;

use Exception;
use Nick;
use Nick\Form\Form;
use Nick\Menu\Entity\Menu;
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
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'menu',
      'title' => $this->translate('Menu'),
      'summary' => $this->translate('Menu options'),
    ]);
    parent::__construct($parameters, $route);
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
  public function render() {
    parent::render();

    if (!$this->hasParameter(2)) {
      return NULL;
    }

    $form = $this->defaultForm((int)$this->get(2));
    if (!$form) {
      $result = '';
    } else {
      $result = $form->result();
    }
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('menu')
      ->render([
        'form' => $result,
      ]);
  }

}
