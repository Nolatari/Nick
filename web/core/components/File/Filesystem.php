<?php

namespace Nick\File;

use Nick\Settings;

/**
 * Class Filesystem
 * @package Nick\File
 */
class Filesystem {

  /** @var array $available */
  protected array $available = [];

  /**
   * Filesystem constructor.
   * Sets available filesystems either by given array or Settings array.
   *
   * @param array|null $available
   */
  public function __construct(?array$available = NULL) {
    $this->setAvailable($available ?? Settings::get('files'));
  }

  /**
   * Returns available filesystems.
   *
   * @return array
   */
  public function getAvailable() {
    return $this->available;
  }

  /**
   * Sets the available filesystems
   *
   * @param array $available
   *
   * @return Filesystem
   */
  protected function setAvailable(array $available): self {
    $this->available = $available;
    return $this;
  }

}
