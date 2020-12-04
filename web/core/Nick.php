<?php

use Nick\Cache\CacheInterface;
use Nick\Config\Config;
use Nick\Core;
use Nick\Database\Database;
use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;
use Nick\Entity\EntityManager;
use Nick\Entity\EntityRenderer;
use Nick\Event\Event;
use Nick\Event\EventInterface;
use Nick\ExtensionManager\ExtensionManager;
use Nick\File\Filesystem;
use Nick\Form\Form;
use Nick\Language\Language;
use Nick\Language\LanguageInterface;
use Nick\Language\LanguageManager;
use Nick\Logger;
use Nick\Manifest\Manifest;
use Nick\Manifest\ManifestInterface;
use Nick\Manifest\ManifestRenderer;
use Nick\Page\PageManager;
use Nick\Person\Entity\Person;
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
use Nick\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Nick helper class.
 */
class Nick {

  /** @var Request $request */
  protected static Request $request;

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
   * @param EntityInterface|null $entity
   *
   * @return Form
   */
  public static function Form(EntityInterface $entity = NULL): Form {
    return new Form($entity);
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
   * Returns current Person object
   *
   * @return EntityInterface
   */
  public static function CurrentPerson(): EntityInterface {
    return Person::load(Person::getCurrentPerson());
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
   * Returns Cache object
   *
   * @return CacheInterface
   */
  public static function Cache(): CacheInterface {
    global $cache;
    return $cache ?? Core::getCacheClass();
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
   * Returns Session object.
   *
   * @return SessionInterface
   */
  public static function Session(): SessionInterface {
    return new Session();
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
   * Returns EntityManager object.
   *
   * @return EntityManager
   */
  public static function EntityManager(): EntityManager {
    return new EntityManager();
  }

  /**
   * Returns Request object.
   *
   * @return Request
   */
  public static function Request(): Request {
    return static::$request;
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
   * Returns Route object.
   *
   * @return RouteInterface
   */
  public static function Route(): RouteInterface {
    return new Route();
  }

  /**
   * Returns the current route object.
   *
   * @return RouteInterface
   */
  public static function CurrentRoute(): RouteInterface {
    return Route::getCurrent();
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
   * Returns PageManager object
   *
   * @return PageManager
   */
  public static function PageManager(): PageManager {
    return new PageManager();
  }

  /**
   * Returns Url object
   *
   * @return Url
   */
  public static function Url(): Url {
    return new Url();
  }

  /**
   * Initialize Nick's dependencies
   */
  public static function Init() {
    $core = new Core();
    $core->setSystemSpecifics();
    static::$request = Request::createFromGlobals();
    static::ExtensionManager()->installExtensions();
    static::EntityManager()->createEntities();
    static::RouteManager()->installRoutes();
  }

  /**
   * Bootstraps Nick
   *
   * @param Request $request
   */
  public static function Bootstrap(Request $request) {
    static::Init();
    $uri = StringManipulation::replace($request->getUri(), Settings::get('root.web.url'), '');
    $route = static::RouteManager()->routeMatch($uri);
    if (!$route) {
      $route = static::Route()->load('dashboard');
    }
    static::Route()::setCurrent($route);

    // Render rest api calls without rendering everything else
    if ($route->isRest()) {
      $route->render();
      exit;
    }

    try {
      $variables = [];
      $variables['logs'] = ['render' => static::Logger()->render()];
      $variables['current_route'] = $route->getRoute();
      $variables['page'] = [
        'title' => $route->getPageObject()->get('title') ?? NULL,
        'author' => $route->getPageObject()->get('author') ?? NULL,
        'summary' => $route->getPageObject()->get('summary') ?? NULL,
      ];
      $header = static::PageManager()->getPageRender('header', $variables, $route);
      $page = $route->render();
      $footer = static::PageManager()->getPageRender('footer', $variables, $route);

      $response = new Response();
      $response->headers->set('Content-Type', 'text/html');
      $response->setContent($header . $page . $footer);
      $response->setStatusCode(Response::HTTP_OK);
      $response->prepare($request);
      $response->send();
    } catch (Exception $exception) {
      static::Logger()->add('Could not render Nick.' . PHP_EOL . $exception->getMessage(), Logger::TYPE_FAILURE, 'Bootstrap');
    }
  }

}