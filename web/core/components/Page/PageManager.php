<?php

namespace Nick\Page;

use Nick\Database\Result;
use Nick\Logger;

/**
 * Class PageManager
 *
 * @package Nick\Page
 */
class PageManager {

  /**
   * @param string $page_id
   * @return bool
   */
  protected function get404($page_id) {
    $page = $this->getPage('error');
    $pageObject = new $page['controller'];
    if (!$pageObject instanceof PageInterface) {
      return FALSE;
    }

    return \Nick::Cache()->getContentData($pageObject->getCacheOptions(), $page['controller'], 'render', [['e' => '404']]);
  }

  /**
   * Returns cached/fresh page content.
   *
   * @param string $page_id
   * @param array  $parameters
   *
   * @return bool|mixed
   */
  public function getPageRender($page_id, $parameters = []) {
    $page = $this->getPage($page_id);
    if (!is_array($page)) {
      return $page;
    }
    $pageObject = new $page['controller'];
    if (!$pageObject instanceof PageInterface) {
      return FALSE;
    }

    return \Nick::Cache()->getContentData($pageObject->getCacheOptions(), $page['controller'], 'render', [$parameters]);
  }

  /**
   * @param string $page_id
   * @param array  $parameters
   *
   * @return bool|PageInterface
   */
  public function getPageObject($page_id, $parameters = []) {
    $page = $this->getPage($page_id);
    if (!is_array($page)) {
      return $this->getPageObject('error');
    }
    $pageObject = new $page['controller'];
    if (!$pageObject instanceof PageInterface) {
      return FALSE;
    }
    return $pageObject;
  }

  /**
   * @param      $page_id
   * @param bool $object
   *
   * @return array|bool|mixed
   */
  public function getPage($page_id, $object = FALSE) {
    $page = \Nick::Database()
      ->select('pages')
      ->condition('id', $page_id)
      ->fields(NULL, ['controller'])
      ->execute();

    if (!$page instanceof Result) {
      \Nick::Logger()->add('Couldn\'t load the page [' . $page_id . ']', Logger::TYPE_ERROR, 'PageManager');
      return FALSE;
    }

    $page_result = $page->fetchAllAssoc();
    if (count($page_result) == 0) {
      \Nick::Logger()->add('Page not found [' . $page_id . ']', Logger::TYPE_FAILURE, 'PageManager');
      return $this->get404($page_id);
    }
    $page_result = reset($page_result);
    return $page_result;
  }

}