<?php

namespace Nick\Article;

use Nick\Matter\MatterInterface;

/**
 * Interface ArticleInterface
 *
 * @package Nick\Article
 */
interface ArticleInterface extends MatterInterface {

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