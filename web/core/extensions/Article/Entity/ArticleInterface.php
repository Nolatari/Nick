<?php

namespace Nick\Article\Entity;

use Nick\Entity\EntityInterface;

/**
 * Interface ArticleInterface
 *
 * @package Nick\Article
 */
interface ArticleInterface extends EntityInterface {

  /**
   * @return string
   */
  public function getTitle();

  /**
   * @param string $title
   *
   * @return bool
   */
  public function setTitle(string $title);

  /**
   * @return string
   */
  public function getBody();

  /**
   * @param string $body
   *
   * @return bool
   */
  public function setBody(string $body);

}