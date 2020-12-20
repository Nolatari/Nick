<?php

namespace Nick\Article\Entity;

use Nick\Entity\Entity;

/**
 * Class Article
 *
 * @package Nick\Article
 */
class Article extends Entity implements ArticleInterface {

  /**
   * Article constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    $this->setValues($values);
    $this->setType('article');
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