<?php

namespace Nick\Conversation;

use Nick\Entity\EntityInterface;
use Nick\Person\PersonInterface;

/**
 * Interface ConversationInterface
 *
 * @package Nick\Conversation
 */
interface ConversationInterface extends EntityInterface {

  /**
   * Gets the initiator of the conversation
   *
   * @return mixed
   */
  public function getSender();

  /**
   * Sets the initiator of the conversation
   *
   * @param PersonInterface $sender
   *
   * @return mixed
   */
  public function setSender(PersonInterface $sender);

  /**
   * Gets the receiver of the conversation
   *
   * @return mixed
   */
  public function getReceiver();

  /**
   * Sets the receiver of the conversation
   *
   * @param PersonInterface $receiver
   *
   * @return mixed
   */
  public function setReceiver(PersonInterface $receiver);

}