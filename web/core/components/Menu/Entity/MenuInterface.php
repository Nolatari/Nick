<?php

namespace Nick\Menu\Entity;

use Nick\Entity\EntityInterface;

/**
 * Interface MenuInterface
 *
 * @package Nick\Menu
 */
interface MenuInterface extends EntityInterface {

  /**
   * @return string
   */
  public function getTitle();

  /**
   * @param string $title
   *
   * @return bool
   */
  public function setTitle(string $title);

  /**
   * @return string
   */
  public function getDescription();

  /**
   * @param string $description
   *
   * @return bool
   */
  public function setDescription(string $description);

  /**
   * @return string
   */
  public function getRoute();

  /**
   * @param string $route
   *
   * @return bool
   */
  public function setRoute(string $route);

  /**
   * @return string
   */
  public function getMenuType();

  /**
   * @param string $type
   *
   * @return bool
   */
  public function setMenuType(string $type);

  /**
   * @return MenuInterface|bool
   */
  public function getParent();

  /**
   * @param int $parent
   *
   * @return bool
   */
  public function setParent(int $parent);

  /**
   * @param bool $translatable
   *
   * @return bool
   */
  public function setTranslatable(bool $translatable);

}