<?php

namespace Nick\Manifest;

/**
 * Interface ManifestInterface
 *
 * @package Nick\Manifest
 */
interface ManifestInterface {

  /**
   * Returns array of results
   *
   * @return array|bool
   */
  public function result();

  /**
   * Sets render mode for results
   *
   * @param bool $render
   *
   * @return $this
   */
  public function render($render = TRUE);

  /**
   * Sets offset and limit for items in query.
   *
   * @param int $limit
   * @param int $offset
   *
   * @return Manifest
   */
  public function limit($limit, $offset);

  /**
   * Sets condition parameters
   *
   * @param string $field
   * @param string $value
   * @param string $delimiter
   *
   * @return Manifest
   */
  public function condition($field, $value, $delimiter = '=');

  /**
   * @param string $field
   * @param string $direction
   *
   * @return Manifest
   */
  public function order($field, $direction = 'ASC');

  /**
   * @param array $fields
   *
   * @return Manifest
   */
  public function fields($fields = []);

}