<?php

namespace Nick\Conversation;

use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;
use Nick\Person\Person;

/**
 * Class Article
 *
 * @package Nick\Conversation
 */
class Message Extends Entity implements MessageInterface {

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
      'conversation' => [
        'type' => 'int',
        'length' => 25,
        'default_value' => Person::getCurrentPerson(),
        'class' => '\\Nick\\Conversation\\Conversation',
      ],
      'message' => [
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
  public function getConversation(): string {
    return $this->getValue('conversation');
  }

  /**
   * {@inheritDoc}
   */
  public function setConversation($conversation) {
    return $this->setValue('conversation', $conversation->id());
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