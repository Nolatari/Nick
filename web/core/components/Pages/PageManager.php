<?php

namespace Nick\Pages;

use Nick\Database\Result;
use Nick\Logger;

/**
 * Class PageManager
 *
 * @package Nick\Pages
 */
class PageManager {

  /**
   * @param string $page_id
   * @return bool
   */
  protected function get404($page_id) {
    $page = \Nick::Database()
      ->select('pages')
      ->condition('id', 'error')
      ->fields(NULL, ['controller', 'cache_type', 'cache_options'])
      ->execute();

    if (!$page instanceof Result) {
      \Nick::Logger()->add('Couldn\'t load the page [' . $page_id . ']', Logger::TYPE_ERROR, 'PageManager');
      return FALSE;
    }

    $page_result = $page->fetchAllAssoc();
    $page_result = reset($page_result);

    return \Nick::Cache()->getContentData(unserialize($page_result['cache_options']), $page_result['controller'], 'render');
  }

  /**
   * Returns cached/fresh page content.
   *
   * @param string $page_id
   *
   * @return bool|mixed
   */
  public function getPage($page_id) {
    $page = \Nick::Database()
      ->select('pages')
      ->condition('id', $page_id)
      ->fields(NULL, ['controller', 'cache_type', 'cache_options'])
      ->execute();

    if (!$page instanceof Result) {
      \Nick::Logger()->add('Couldn\'t load the page [' . $page_id . ']', Logger::TYPE_ERROR, 'PageManager');
      return FALSE;
    }

    $page_result = $page->fetchAllAssoc();
    if (count($page_result) == 0) {
      \Nick::Logger()->add('Page not found [' . $page . ']', Logger::TYPE_FAILURE, 'PageManager');
      return $this->get404($page_id);
    }
    $page_result = reset($page_result);

    return \Nick::Cache()->getContentData(unserialize($page_result['cache_options']), $page_result['controller'], 'render');
  }

}