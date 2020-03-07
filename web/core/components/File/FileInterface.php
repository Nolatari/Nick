<?php

namespace Nick\File;

use Nick\Matter\MatterInterface;

/**
 * Interface FileInterface
 *
 * @package Nick\File
 */
interface FileInterface extends MatterInterface {

  /**
   * @return array|mixed|NULL|string
   */
  public function getFileType();

  /**
   * @return array|mixed|NULL|string
   */
  public function getLocation();

}