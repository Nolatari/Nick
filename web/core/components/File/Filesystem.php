<?php

namespace Nick\File;

use Nick\Settings;

/**
 * Class Filesystem
 *
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
  public function __construct(?array $available = NULL) {
    $this->setAvailable($available ?? Settings::get('files'));
  }

  /**
   * Returns available filesystems.
   *
   * @param string|null $filesystem
   *
   * @return array|bool
   */
  public function getAvailable(string $filesystem = NULL) {
    if (!is_null($filesystem)) {
      if (!isset($this->available[$filesystem])) {
        return FALSE;
      }
      return $this->available[$filesystem];
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

  /**
   * @param string $filesystem
   *
   * @return false|string
   */
  public function getFolder(string $filesystem) {
    if (!isset($this->available[$filesystem]['folder'])) {
      return FALSE;
    }

    return $this->available[$filesystem]['folder'];
  }

  /**
   * @param string $filesystem
   *
   * @return false|string
   */
  public function getUrl(string $filesystem) {
    if (!isset($this->available[$filesystem]['url'])) {
      return FALSE;
    }

    return $this->available[$filesystem]['url'];
  }

}
