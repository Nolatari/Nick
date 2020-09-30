<?php

namespace Nick\Pages;

use Nick\Entity\EntityInterface;

/**
 * Interface PagesInterface
 *
 * @package Nick\Pages
 */
interface PageInterface extends EntityInterface {

  /**
   * @return string
   */
  public function getPid();

  /**
   * @param string $pid
   *
   * @return bool
   */
  public function setPid(string $pid);

  /**
   * @return string
   */
  public function getController();

  /**
   * @param string $controller
   *
   * @return bool
   */
  public function setController(string $controller);

}