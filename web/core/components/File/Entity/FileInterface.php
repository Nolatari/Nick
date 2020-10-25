<?php

namespace Nick\File\Entity;

use Nick\Entity\EntityInterface;

/**
 * Interface FileInterface
 *
 * @package Nick\File
 */
interface FileInterface extends EntityInterface {

  /**
   * @return array|mixed|NULL|string
   */
  public function getFileType();

  /**
   * @param string $filetype
   *
   * @return self
   */
  public function setFileType(string $filetype);

  /**
   * @return array|mixed|NULL|string
   */
  public function getLocation();

}