<?php

namespace Nick\Conversation\Entity;

use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;
use Nick\Person\Entity\Person;
use Nick\Person\Entity\PersonInterface;

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
    $this->setValues($values);
    $this->setType('conversation');
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
        'class' => '\\Nick\\Person\\Entity\\Person',
      ],
      'receiver' => [
        'type' => 'int',
        'length' => 25,
        'default_value' => Person::getCurrentPerson(),
        'class' => '\\Nick\\Person\\Entity\\Person',
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