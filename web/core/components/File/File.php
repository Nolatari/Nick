<?php

namespace Nick\File;

use Nick\Matter\Matter;
use Nick\Matter\MatterInterface;

/**
 * Class File
 *
 * @package Nick\File
 */
class File extends Matter implements FileInterface {

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
   * @return Matter|MatterInterface|null
   */
  public static function load(int $id) {
    return parent::loadMatter($id, 'file');
  }

  /**
   * @return array
   */
  public static function loadMultiple() {
    return parent::loadMultipleMatters('file');
  }

  /**
   * @return string|null
   */
  public static function create() {
    return parent::createMatter('file');
  }

  /**
   * @return array
   */
  public static function initialFields() {
    return [
      'filetype' => [
        'type' => 'varchar',
        'length' => 255,
        'default_value' => static::TYPE_FILE,
        'form' => [
          'type' => 'select',
          'name' => 'file_type',
          'label' => 'Type',
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
          'label' => 'Location',
        ],
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getFileType() {
    return $this->getValue('filetype');
  }

  /**
   * {@inheritDoc}
   */
  public function getLocation() {
    return $this->getValue('location');
  }

}