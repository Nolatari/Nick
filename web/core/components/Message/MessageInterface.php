<?php

namespace Nick\Message;

use Nick\Conversation\ConversationInterface;
use Nick\Entity\EntityInterface;

/**
 * Interface MessageInterface
 *
 * @package Nick\Message
 */
interface MessageInterface extends EntityInterface {

  /**
   * Gets conversation ID
   *
   * @return mixed
   */
  public function getConversation();

  /**
   * Sets conversation for message
   *
   * @param ConversationInterface $conversation
   *
   * @return mixed
   */
  public function setConversation(ConversationInterface $conversation);

  /**
   * Gets message body
   *
   * @return mixed
   */
  public function getBody();

  /**
   * Sets message body
   *
   * @param string $body
   *
   * @return mixed
   */
  public function setBody(string $body);

}