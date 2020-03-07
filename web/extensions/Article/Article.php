<?php

namespace Nick\Extension\Article;

use Nick\Matter\Matter;
use Nick\Matter\MatterInterface;

/**
 * Class Article
 *
 * @package Nick\Extension\Article
 */
class Article Extends Matter implements ArticleInterface {

  /**
   * Card constructor.
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
   * @return Matter|MatterInterface|NULL
   */
  public static function load(int $id) {
    return parent::loadMatter($id, 'article');
  }

  /**
   * @return array
   */
  public static function loadMultiple() {
    return parent::loadMultipleMatters('article');
  }

  /**
   * @return string|null
   */
  public static function create() {
    return parent::createMatter('article');
  }

  /**
   * @return array
   */
  public static function initialFields() {
    return [
        'title' => [
          'type' => 'varchar',
          'length' => 255,
          'form' => [
            'type' => 'textbox',
            'name' => 'title',
            'required' => TRUE,
          ],
        ],
        'body' => [
          'type' => 'text',
          'form' => [
            'type' => 'wysiwyg',
            'name' => 'body',
            'required' => TRUE,
          ],
        ],
      ];
  }

  /**
   * @inheritDoc
   */
  public function getTitle() {
    return $this->getValue('title');
  }

  /**
   * @inheritDoc
   */
  public function setTitle($title) {
    return $this->setValue('title', $title);
  }

  /**
   * @inheritDoc
   */
  public function getBody() {
    return $this->getValue('body');
  }

  /**
   * @inheritDoc
   */
  public function setBody($body) {
    return $this->setValue('body', $body);
  }
}