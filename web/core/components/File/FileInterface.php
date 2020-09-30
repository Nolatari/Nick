<?php

namespace Nick\File;

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
   * @return array|mixed|NULL|string
   */
  public function getLocation();

}