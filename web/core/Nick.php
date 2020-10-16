<?php

use Nick\Cache\CacheInterface;
use Nick\Config\Config;
use Nick\Core;
use Nick\Database\Database;
use Nick\Entity\EntityRenderer;
use Nick\Event\Event;
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
use Nick\Page\PageManager;
use Nick\Person\Person;
use Nick\Person\PersonInterface;
use Nick\Renderer;
use Nick\Route\Route;
use Nick\Route\RouteManager;
use Nick\Search\Search;
use Nick\Settings;
use Nick\StringManipulation;
use Nick\Theme;
use Nick\Translation\Translation;
use Nick\Translation\TranslationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
   * Returns Event object
   *
   * @param string $eventName
   *
   * @return Event
   */
  public static function Event(string $eventName) {
    return new Event($eventName);
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
   * Returns Cache object
   *
   * @return CacheInterface
   */
  public static function Cache() {
    global $cache;
    return $cache ?? Core::getCacheClass();
  }

  /**
   * Returns Person object
   *
   * @return PersonInterface
   */
  public static function CurrentPerson() {
    return Person::load(Person::getCurrentPerson());
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
    if (!is_null($database)) {
      return self::Cache()->getData('database.' . $database . '.' . $condition_delimiter, '\\Nick\\Database\\Database', NULL, [], [$condition_delimiter, $database]);
    } else {
      return self::Cache()->getData('database.default.' . $condition_delimiter, '\\Nick\\Database\\Database', NULL, [], [$condition_delimiter]);
    }
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
   *
   *
   * @return SessionInterface
   */
  public static function Session() {
    return new Session();
  }

  /**
   * Bootstraps Nick
   *
   * @param Request $request
   */
  public static function Bootstrap(Request $request) {
    $core = new Core();
    $core->setSystemSpecifics();
    static::ExtensionManager()->installExtensions();
    static::EntityManager()->createEntities();
    static::RouteManager()->installRoutes();

    $uri = StringManipulation::replace($request->getUri(), Settings::get('root.url'), '');
    $route = static::RouteManager()->routeMatch($uri);
    if (!$route) {
      $route = static::Route()->load('error')->setValue('key', '404');
    }

    try {
      $headerVariables = [];
      $headerVariables['logs'] = ['render' => static::Logger()->render()];
      $headerVariables['current_route'] = $route->getRoute();
      $headerVariables['page'] = [
        'title' => $route->getPageObject()->get('title') ?? NULL,
        'summary' => $route->getPageObject()->get('summary') ?? NULL,
        'author' => $route->getPageObject()->get('author') ?? NULL,
      ];
      $header = static::PageManager()->getPageRender('header', $headerVariables, $route);
      $page = $route->render();
      $footer = static::PageManager()->getPageRender('footer', [], $route);

      echo $header ?? NULL;
      echo $page ?? NULL;
      echo $footer ?? NULL;
    } catch (Exception $exception) {
      static::Logger()->add('Could not render Nick!' . PHP_EOL . $exception->getMessage(), Logger::TYPE_FAILURE, 'Bootstrap');
    }
  }

}