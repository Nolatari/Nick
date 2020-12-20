<?php

namespace Nick\Page;

use Nick;
use Nick\Database\Result;
use Nick\Logger;
use Nick\Route\RouteInterface;

/**
 * Class PageManager
 *
 * @package Nick\Page
 */
class PageManager {

  /** @var array $parameters */
  protected array $parameters = [];

  /** @var RouteInterface $route */
  protected RouteInterface $route;

  /**
   * PageManager constructor.
   *
   * @param array          $parameters
   * @param RouteInterface $route
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->parameters = $parameters;
    $this->route = $route;
  }

  /**
   * Returns cached/fresh page content.
   *
   * @param string $page_id
   *
   * @return mixed
   */
  public function getPageRender(string $page_id) {
    $page = $this->getPage($page_id);
    if (!is_array($page)) {
      return $page;
    }
    $pageObject = new $page['controller']($this->parameters, $this->route);
    if (!$pageObject instanceof PageInterface) {
      return FALSE;
    }
    return \Nick::Cache()->getContentData($pageObject->getCacheOptions(), $page['controller'], 'render', [], [$this->parameters, $this->route]);
  }

  /**
   * @param string $page_id
   *
   * @return bool|PageInterface
   */
  public function getPageObject(string $page_id) {
    $page = $this->getPage($page_id);
    if (!is_array($page)) {
      return $this->getPageObject('error');
    }
    $pageObject = new $page['controller']($this->parameters, $this->route);
    if (!$pageObject instanceof PageInterface) {
      return FALSE;
    }
    return $pageObject;
  }

  /**
   * Creates page in database.
   *
   * @param array $values
   *
   * @return bool
   */
  public function createPage($values = []): bool {
    $page = \Nick::Database()
      ->insert('pages')
      ->values($values);
    if (!$page->execute()) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * @param string $page_id
   *
   * @return mixed
   */
  protected function get404(string $page_id) {
    $page = $this->getPage('error');
    $pageObject = new $page['controller']($this->parameters, $this->route);
    if (!$pageObject instanceof PageInterface) {
      return FALSE;
    }

    return \Nick::Cache()->getContentData($pageObject->getCacheOptions(), $page['controller'], 'render', [], [['e' => '404', 'page' => $page_id], $this->route]);
  }

  /**
   * @param string $page_id
   *
   * @return array|bool|mixed
   */
  public function getPage(string $page_id) {
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