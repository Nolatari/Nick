<?php

namespace Nick\Pages;

use Nick\Matter\MatterInterface;

/**
 * Interface PagesInterface
 *
 * @package Nick\Pages
 */
interface PagesInterface extends MatterInterface {

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