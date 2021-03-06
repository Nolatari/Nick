<?php

namespace Nick\Conversation\Entity;

use Nick\Entity\Entity;

/**
 * Class Message
 *
 * @package Nick\Message
 */
class Message extends Entity implements MessageInterface {

  /**
   * Message constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    $this->setValues($values);
    $this->setType('message');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    return [
      'conversation' => [
        'type' => 'int',
        'length' => 25,
        'class' => '\\Nick\\Conversation\\Entity\\Conversation',
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
  public function setConversation(ConversationInterface $conversation) {
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