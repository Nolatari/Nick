<?php

use Nick\Cache\CacheInterface;
use Nick\Config\Config;
use Nick\Core;
use Nick\Database\Database;
use Nick\Entity\EntityRenderer;
use Nick\Event\Event;
use Nick\Event\EventInterface;
use Nick\ExtensionManager\ExtensionManager;
use Nick\File\Filesystem;
use Nick\Form\Form;
use Nick\Form\FormInterface;
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
use Nick\Person\Entity\Person;
use Nick\Person\Entity\PersonInterface;
use Nick\Renderer;
use Nick\Route\Route;
use Nick\Route\RouteInterface;
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
   * @return EntityInterface
   */
  public static function Entity(): EntityInterface {
    return new Entity();
  }

  /**
   * Returns Event object
   *
   * @param string $eventName
   *
   * @return EventInterface
   */
  public static function Event(string $eventName): EventInterface {
    return new Event($eventName);
  }

  /**
   * Returns EntityRenderer object
   *
   * @param EntityInterface $entity
   *
   * @return EntityRenderer
   */
  public static function EntityRenderer(EntityInterface $entity): EntityRenderer {
    return new EntityRenderer($entity);
  }

  /**
   * Returns Form object
   *
   * @param EntityInterface|null $matter
   *
   * @return FormInterface
   */
  public static function Form(EntityInterface $matter = NULL): FormInterface {
    return new Form($matter);
  }

  /**
   * Returns Search object
   *
   * @param $query
   *
   * @return Search
   */
  public static function Search($query): Search {
    return new Search($query);
  }

  /**
   * Returns Renderer object.
   *
   * @return Renderer
   */
  public static function Renderer(): Renderer {
    return new Renderer();
  }

  /**
   * Returns Translation object
   *
   * @return TranslationInterface
   */
  public static function Translation(): TranslationInterface {
    return new Translation();
  }

  /**
   * Returns Config object
   *
   * @return Config
   */
  public static function Config(): Config {
    return new Config();
  }

  /**
   * Returns Cache object
   *
   * @return CacheInterface
   */
  public static function Cache(): CacheInterface {
    global $cache;
    return $cache ?? Core::getCacheClass();
  }

  /**
   * Returns Person object
   *
   * @return PersonInterface
   */
  public static function CurrentPerson(): PersonInterface {
    return Person::load(Person::getCurrentPerson());
  }

  /**
   * Returns ExtensionManager object
   *
   * @return ExtensionManager
   */
  public static function ExtensionManager(): ExtensionManager {
    return new ExtensionManager();
  }

  /**
   * Returns Filesystem object
   *
   * @param array|null $available
   *
   * @return Filesystem
   */
  public static function Filesystem(?array $available = NULL): Filesystem {
    return new Filesystem($available);
  }

  /**
   * Returns LanguageManager object
   *
   * @return LanguageManager
   */
  public static function LanguageManager(): LanguageManager {
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
  public static function Theme(): Theme {
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
  public static function Database($condition_delimiter = 'AND', $database = NULL): Database {
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
  public static function EntityManager(): EntityManager {
    return new EntityManager();
  }

  /**
   * Returns PageManager object
   *
   * @return PageManager
   */
  public static function PageManager(): PageManager {
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
  public static function ManifestRenderer(ManifestInterface $manifest): ManifestRenderer {
    return new ManifestRenderer($manifest);
  }

  /**
   * Returns Logger object
   *
   * @return Logger
   */
  public static function Logger(): Logger {
    return new Logger();
  }

  /**
   * Returns Route object.
   *
   * @return RouteInterface
   */
  public static function Route(): RouteInterface {
    return new Route();
  }

  /**
   * Returns Route object.
   *
   * @return RouteManager
   */
  public static function RouteManager(): RouteManager {
    return new RouteManager();
  }

  /**
   * Returns Session object.
   *
   * @return SessionInterface
   */
  public static function Session(): SessionInterface {
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

    $uri = StringManipulation::replace($request->getUri(), Settings::get('root.web.url'), '');
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