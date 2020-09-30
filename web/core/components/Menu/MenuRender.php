<?php

namespace Nick\Menu;

use Exception;
use Nick;

/**
 * Class MenuRender
 *
 * @package Nick\Menu
 */
class MenuRender {

  /**
   * @param $parameters
   * @param $page_id
   *
   * @throws Exception
   */
  public function pagePreRender(&$parameters, $page_id) {
    if ($page_id !== 'header') {
      return;
    }

    // Load menu items
    $menus = Nick::Manifest('menu')
      ->fields(['id', 'title', 'description', 'route', 'type', 'parent'])
      ->condition('status', 1)
      ->order('structure', 'ASC')
      ->result();

    // Loop over menus and add to array
    foreach ($menus as $key => $menu) {
      $menus[$key]['route'] = explode('.', $menus[$key]['route']);
      $children = Nick::EntityManager()->loadByProperties(['type' => 'menu', 'parent' => $menus[$key]['id']]);
      if ($children !== FALSE) {
        foreach ($children as &$child) {
          $child = $child->getValues();
          $child['route'] = explode('.', $child['route']);
        }
        $menus[$key]['children'] = $children;
      }
      if ($menus[$key]['parent'] != 0) {
        unset($menus[$key]);
      }
    }

    // Add menu to array
    $parameters['menu'] = $menus;
  }

}