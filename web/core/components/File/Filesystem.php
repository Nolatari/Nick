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
   * @param string|null $key
   *
   * @return array|bool
   */
  public function getAvailable(string $key = NULL) {
    if (!is_null($key)) {
      if (!isset($this->available[$key])) {
        return FALSE;
      }
      return $this->available[$key];
    }
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
