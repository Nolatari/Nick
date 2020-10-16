<?php

namespace Nick\File\Entity;

use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;

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
    parent::__construct($values);
    $this->setType('file');
  }

  /**
   * @param int $id
   *
   * @return Entity|EntityInterface|null
   */
  public static function load(int $id) {
    return parent::loadEntity($id, 'file');
  }

  /**
   * @return array
   */
  public static function loadMultiple() {
    return parent::loadMultipleEntities('file');
  }

  /**
   * @return string|null
   */
  public static function create() {
    return parent::createEntity('file');
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
          'title' => 'Location',
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
  public function getLocation(): string {
    return $this->getValue('location');
  }

}