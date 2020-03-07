<?php

namespace Nick\Extension\Article;

use Nick\Matter\MatterInterface;

/**
 * Interface UserInterface
 *
 * @package Nick\Extension\Article
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