<?php

namespace Nick\Article;

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
  public function setTitle($title);

  /**
   * @return string
   */
  public function getBody();

  /**
   * @param string $body
   *
   * @return bool
   */
  public function setBody($body);

}