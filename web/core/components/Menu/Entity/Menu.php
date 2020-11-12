<?php

namespace Nick\Menu\Entity;

use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;

/**
 * Class Article
 *
 * @package Nick\Menu
 */
class Menu extends Entity implements MenuInterface {

  /**
   * Card constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    $this->setValues($values);
    $this->setType('menu');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    return [
      'structure' => [
        'type' => 'int',
        'length' => 25,
        'form' => [
          'type' => 'textbox',
          'title' => 'Structure',
          'attributes' => [
            'name' => 'structure',
          ],
          'required' => TRUE,
        ],
      ],
      'title' => [
        'type' => 'varchar',
        'length' => 255,
        'form' => [
          'type' => 'textbox',
          'title' => 'Title',
          'attributes' => [
            'name' => 'title',
          ],
          'required' => TRUE,
        ],
      ],
      'description' => [
        'type' => 'text',
        'form' => [
          'type' => 'textbox',
          'title' => 'Description',
          'attributes' => [
            'name' => 'description',
          ],
          'required' => FALSE,
        ],
      ],
      'route' => [
        'type' => 'varchar',
        'length' => 255,
        'form' => [
          'type' => 'textbox',
          'title' => 'Route',
          'attributes' => [
            'name' => 'route',
          ],
          'required' => TRUE,
        ],
      ],
      'translatable' => [
        'type' => 'tinyint',
        'length' => 1,
        'form' => [
          'type' => 'checkbox',
          'title' => 'Translatable',
          'attributes' => [
            'name' => 'translatable',
          ],
        ],
      ],
      'type' => [
        'type' => 'text',
        'length' => 50,
        'form' => [
          'type' => 'textbox',
          'title' => 'Type',
          'attributes' => [
            'name' => 'type',
          ],
          'required' => TRUE,
        ],
      ],
      'parent' => [
        'type' => 'int',
        'length' => 25,
        'form' => [
          'type' => 'textbox',
          'title' => 'Parent',
          'attributes' => [
            'name' => 'parent',
          ],
          'required' => TRUE,
        ],
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getTitle(): string {
    return $this->getValue('title');
  }

  /**
   * {@inheritDoc}
   */
  public function setTitle(string $title) {
    return $this->setValue('title', $title);
  }

  /**
   * {@inheritDoc}
   */
  public function getDescription(): string {
    return $this->getValue('description');
  }

  /**
   * {@inheritDoc}
   */
  public function setDescription($description) {
    return $this->setValue('description', $description);
  }

  /**
   * {@inheritDoc}
   */
  public function getRoute() {
    return $this->getValue('route');
  }

  /**
   * {@inheritDoc}
   */
  public function setRoute(string $route) {
    return $this->setValue('route', $route);
  }

  /**
   * {@inheritDoc}
   */
  public function getMenuType() {
    return $this->getValue('type');
  }

  /**
   * {@inheritDoc}
   */
  public function setMenuType(string $type) {
    return $this->setValue('type', $type);
  }

  /**
   * {@inheritDoc}
   */
  public function getParent() {
    $parentId = $this->getValue('parent');
    if ($parentId !== 0) {
      return Menu::load($parentId);
    }
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function setParent(int $parent) {
    return $this->setValue('parent', $parent);
  }

  /**
   * {@inheritDoc}
   */
  public function setTranslatable(bool $translatable) {
    return $this->setValue('translatable', $translatable);
  }

}
