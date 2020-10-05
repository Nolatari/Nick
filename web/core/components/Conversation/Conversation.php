<?php

namespace Nick\Conversation;

use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;
use Nick\Person\Person;
use Nick\Person\PersonInterface;

/**
 * Class Conversation
 *
 * @package Nick\Conversation
 */
class Conversation Extends Entity implements ConversationInterface {

  /**
   * Conversation constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    parent::__construct($values);
    $this->setType('conversation');
  }

  /**
   * @param int $id
   *
   * @return EntityInterface|NULL
   *
   * @throws \Exception
   */
  public static function load(int $id) {
    return parent::loadEntity($id, 'conversation');
  }

  /**
   * @return array
   */
  public static function loadMultiple() {
    return parent::loadMultipleEntities('conversation');
  }

  /**
   * @return string|null
   */
  public static function create() {
    return parent::createEntity('conversation');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    return [
      'sender' => [
        'type' => 'int',
        'length' => 25,
        'default_value' => Person::getCurrentPerson(),
        'class' => '\\Nick\\Person\\Person',
      ],
      'receiver' => [
        'type' => 'int',
        'length' => 25,
        'default_value' => Person::getCurrentPerson(),
        'class' => '\\Nick\\Person\\Person',
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getSender(): string {
    return $this->getValue('sender');
  }

  /**
   * {@inheritDoc}
   */
  public function setSender(PersonInterface $sender) {
    return $this->setValue('sender', $sender->id());
  }

  /**
   * {@inheritDoc}
   */
  public function getReceiver(): string {
    return $this->getValue('receiver');
  }

  /**
   * {@inheritDoc}
   */
  public function setReceiver(PersonInterface $receiver) {
    return $this->setValue('receiver', $receiver->id());
  }
}