<?php

namespace Nick\Page;

use Nick;
use Nick\Database\Result;
use Nick\Logger;
use Nick\Route\RouteInterface;

/**
 * Class ElementManager
 *
 * @package Nick\Page
 */
class ElementManager {

  /**
   * Returns cached/fresh page content.
   *
   * @param string         $element_id
   * @param array          $parameters
   * @param RouteInterface $route
   *
   * @return mixed
   */
  public function getElementRender(string $element_id, array $parameters, RouteInterface $route) {
    $element = $this->getElement($element_id);
    if (!is_array($element)) {
      return $element;
    }
    $elementObject = new $element['controller'];
    if (!$elementObject instanceof ElementInterface) {
      return FALSE;
    }
    return \Nick::Cache()->getContentData($elementObject->getCacheOptions(), $element['controller'], 'render', [$parameters, $route]);
  }

  /**
   * @param string $element_id
   * @param array  $parameters
   *
   * @return bool|ElementInterface
   */
  public function getElementObject(string $element_id, $parameters = []) {
    $element = $this->getElement($element_id);
    if (!is_array($element)) {
      return NULL;
    }
    $elementObject = new $element['controller'];
    if (!$elementObject instanceof ElementInterface) {
      return FALSE;
    }
    return $elementObject;
  }

  /**
   * @param      $element_id
   * @param bool $object
   *
   * @return array|bool|mixed
   */
  public function getElement($element_id, $object = FALSE) {
    $element = \Nick::Database()
      ->select('elements')
      ->condition('id', $element_id)
      ->fields(NULL, ['controller'])
      ->execute();

    if (!$element instanceof Result) {
      \Nick::Logger()->add('Couldn\'t load the page [' . $element_id . ']', Logger::TYPE_ERROR, 'ElementManager');
      return FALSE;
    }

    $element_result = $element->fetchAllAssoc();
    if (count($element_result) == 0) {
      \Nick::Logger()->add('Element not found [' . $element_id . ']', Logger::TYPE_FAILURE, 'ElementManager');
      return NULL;
    }
    $element_result = reset($element_result);
    return $element_result;
  }

}