<?php

namespace Nick\Article\Entity;

use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;

/**
 * Class Article
 *
 * @package Nick\Article
 */
class Article Extends Entity implements ArticleInterface {

  /**
   * Article constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    parent::__construct($values);
    $this->setType('article');
  }

  /**
   * @param int $id
   *
   * @return EntityInterface|NULL
   */
  public static function load(int $id) {
    return parent::loadEntity($id, 'article');
  }

  /**
   * @return array
   */
  public static function loadMultiple() {
    return parent::loadMultipleEntities('article');
  }

  /**
   * @return string|null
   */
  public static function create() {
    return parent::createEntity('article');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    return [
      'title' => [
        'type' => 'varchar',
        'length' => 255,
        'form' => [
          'title' => 'Title',
          'type' => 'textbox',
          'name' => 'title',
          'required' => TRUE,
        ],
      ],
      'body' => [
        'type' => 'text',
        'form' => [
          'title' => 'Body',
          'type' => 'wysiwyg',
          'name' => 'body',
          'attributes' => [
            'rows' => 5,
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
  public function getBody(): string {
    return $this->getValue('body');
  }

  /**
   * {@inheritDoc}
   */
  public function setBody(string $body) {
    return $this->setValue('body', $body);
  }
}