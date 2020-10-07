<?php

use Nick\Cache\CacheInterface;
use Nick\Config\Config;
use Nick\Core;
use Nick\Database\Database;
use Nick\Entity\EntityRenderer;
use Nick\ExtensionManager\ExtensionManager;
use Nick\Form\Form;
use Nick\Language\Language;
use Nick\Language\LanguageInterface;
use Nick\Language\LanguageManager;
use Nick\Logger;
use Nick\Manifest\Manifest;
use Nick\Manifest\ManifestInterface;
use Nick\Manifest\ManifestRenderer;
use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;
use Nick\Entity\EntityManager;
use Nick\Page\PageInterface;
use Nick\Page\PageManager;
use Nick\Renderer;
use Nick\Route\Route;
use Nick\Route\RouteManager;
use Nick\Search\Search;
use Nick\Settings;
use Nick\StringManipulation;
use Nick\Theme;
use Nick\Translation\Translation;
use Nick\Translation\TranslationInterface;
use Nick\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Nick helper class.
 */
class Nick {

  /**
   * Returns Entity object
   *
   * @return Entity
   */
  public static function Entity() {
    return new Entity();
  }

  /**
   * Returns EntityRenderer object
   *
   * @param EntityInterface $entity
   *
   * @return EntityRenderer
   */
  public static function EntityRenderer(EntityInterface $entity) {
    return new EntityRenderer($entity);
  }

  /**
   * Returns Form object
   *
   * @param EntityInterface|null $matter
   *
   * @return Form
   */
  public static function Form(EntityInterface $matter = NULL) {
    return new Form($matter);
  }

  /**
   * Returns Search object
   *
   * @param $query
   *
   * @return Search
   */
  public static function Search($query) {
    return new Search($query);
  }

  /**
   * Returns Renderer object.
   *
   * @return Renderer
   */
  public static function Renderer() {
    return new Renderer();
  }

  /**
   * Returns Translation object
   *
   * @return TranslationInterface
   */
  public static function Translation() {
    return new Translation();
  }

  /**
   * Returns Config object
   *
   * @return Config
   */
  public static function Config() {
    return new Config();
  }

  /**
   * @return CacheInterface
   */
  public static function Cache() {
    global $cache;
    return $cache ?? Core::getCacheClass();
  }

  /**
   * Returns ExtensionManager object
   *
   * @return ExtensionManager
   */
  public static function ExtensionManager() {
    return new ExtensionManager();
  }

  /**
   * Returns LanguageManager object
   *
   * @return LanguageManager
   */
  public static function LanguageManager() {
    return new LanguageManager();
  }

  /**
   * Returns Language object
   *
   * @param string $langcode
   *
   * @return LanguageInterface
   */
  public static function Language(string $langcode = 'en'): LanguageInterface {
    return new Language($langcode);
  }

  /**
   * Returns Theme object
   *
   * @return Theme
   */
  public static function Theme() {
    return new Theme();
  }

  /**
   * Returns cached Database object
   *
   * @param string $condition_delimiter
   * @param null   $database
   *
   * @return Database
   */
  public static function Database($condition_delimiter = 'AND', $database = NULL) {
    return new Database($condition_delimiter, $database); // Find solution to  cache Database again but with dynamic parameters
    //return self::Cache()->getData('database', '\\Nick\\Database\\Database', NULL, [], [$condition_delimiter, $database]);
  }

  /**
   * Returns EntityManager object.
   *
   * @return EntityManager
   */
  public static function EntityManager() {
    return new EntityManager();
  }

  /**
   * Returns PageManager object
   *
   * @return PageManager
   */
  public static function PageManager() {
    return new PageManager();
  }

  /**
   * Returns Manifest object
   *
   * @param string $type
   *
   * @return ManifestInterface
   */
  public static function Manifest(string $type): ManifestInterface {
    return new Manifest($type);
  }

  /**
   * Returns Manifest object
   *
   * @param ManifestInterface $manifest
   *
   * @return ManifestRenderer
   */
  public static function ManifestRenderer(ManifestInterface $manifest) {
    return new ManifestRenderer($manifest);
  }

  /**
   * Returns Logger object
   *
   * @return Logger
   */
  public static function Logger() {
    return new Logger();
  }

  /**
   * Returns Route object.
   *
   * @return Route
   */
  public static function Route() {
    return new Route();
  }

  /**
   * Returns Route object.
   *
   * @return RouteManager
   */
  public static function RouteManager() {
    return new RouteManager();
  }

  /**
   * Bootstraps Nick
   *
   * @param Request $request
   */
  public static function Bootstrap(Request $request) {
    $core = new Core();
    $core->setSystemSpecifics();
    self::EntityManager()->createEntities();
    self::ExtensionManager()->installExtensions();

    $uri = StringManipulation::replace($request->getUri(), Settings::get('root.url'), '');
    $route = static::RouteManager()->routeMatch(StringManipulation::replace($uri, Settings::get('root.url'), ''));
    if (!$route) {
      $route = static::Route()->load('dashboard');
    }

    //d($route);

    $page = $request->query->has('p') ? $request->query->get('p') : 'dashboard';
    $type = $request->query->has('t') ? $request->query->get('t') : NULL;
    $id = $request->query->has('id') ? $request->query->get('id') : NULL;

    try {
      $logger = new Logger();
      $pageObject = self::PageManager()->getPageObject($page, $parameters = Url::getParameters());
      $headerVariables = [];
      $headerVariables['logs'] = ['render' => $logger->render()];
      $headerVariables['current_route'] = [
        'route' => Url::getCurrentRoute()->getUri(),
        'page' => $page,
        'type' => $type,
        'id' => $id,
      ];
      if ($pageObject instanceof PageInterface) {
        $headerVariables['page'] = [
          'id' => $page,
          'type' => $pageObject->get('type'),
          'title' => $pageObject->get('title'),
          'summary' => $pageObject->get('summary'),
          'cacheclear_uri' => Url::fromRoute(static::Route()->load('cache.clear')),
        ];
      }
      $header = self::PageManager()->getPageRender('header', $headerVariables);
      $page = $route->render();
      $footer = self::PageManager()->getPageRender('footer');

      echo $header ?? NULL;
      echo $page ?? NULL;
      echo $footer ?? NULL;
    } catch (Exception $exception) {
      self::Logger()->add('Could not render Nick!' . PHP_EOL . $exception->getMessage(), Logger::TYPE_FAILURE, 'Bootstrap');
    }
  }

}