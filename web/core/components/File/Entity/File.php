<?php

namespace Nick\File\Entity;

use Nick\Entity\Entity;

/**
 * Class File
 *
 * @package Nick\File
 */
class File extends Entity implements FileInterface {

  /** File type constants */
  const TYPE_VIDEO = 'video';
  const TYPE_IMAGE = 'image';
  const TYPE_FILE = 'file';

  /**
   * Card constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    $this->setValues($values);
    $this->setType('file');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    return [
      'filetype' => [
        'type' => 'varchar',
        'length' => 255,
        'default_value' => static::TYPE_FILE,
        'form' => [
          'type' => 'select',
          'name' => 'file_type',
          'title' => 'Type',
          'options' => [
            static::TYPE_FILE => 'File',
            static::TYPE_IMAGE => 'Image',
            static::TYPE_VIDEO => 'Video',
          ],
        ],
      ],
      'location' => [
        'type' => 'varchar',
        'length' => 255,
        'form' => [
          'type' => 'file',
          'name' => 'file_location',
          'title' => 'File Location',
        ],
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getFileType(): string {
    return $this->getValue('filetype');
  }

  /**
   * {@inheritDoc}
   */
  public function setFileType(string $filetype): self {
    return $this->setValue('filetype', $filetype);
  }

  /**
   * {@inheritDoc}
   */
  public function getLocation(): string {
    return $this->getValue('location');
  }

}