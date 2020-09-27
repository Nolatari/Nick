<?php

use Nick\Cache\CacheInterface;
use Nick\Config\Config;
use Nick\Core;
use Nick\Database\Database;
use Nick\ExtensionManager\ExtensionManager;
use Nick\Form\Form;
use Nick\Language\Language;
use Nick\Language\LanguageManager;
use Nick\Logger;
use Nick\Manifest\Manifest;
use Nick\Matter\Matter;
use Nick\Matter\MatterInterface;
use Nick\Matter\MatterManager;
use Nick\Menu\Menu;
use Nick\Page\PageInterface;
use Nick\Page\PageManager;
use Nick\Renderer;
use Nick\Theme;
use Nick\Translation\TranslationInterface;
use Nick\Url;

/**
 * Nick helper class.
 */
class Nick {

  /**
   * Returns non-cached Matter object
   *
   * @return Matter
   */
  public static function Matter() {
    return new Matter();
  }

  /**
   * Returns non-cached FormBuilder object
   *
   * @param MatterInterface|null $matter
   *
   * @return Form
   */
  public static function Form(MatterInterface $matter = NULL) {
    return new Form($matter);
  }

  /**
   * Returns non-cached Renderer object.
   *
   * @return Renderer
   */
  public static function Renderer() {
    return new Renderer();
  }

  /**
   * Returns cached Translation object
   *
   * @return TranslationInterface
   */
  public static function Translation() {
    $translationExtension = self::Config()->get('translation.extension') or 'Translation';
    if (!self::ExtensionManager()::extensionInstalled($translationExtension)) {
      $translationExtension = 'Translation';
    }
    return self::Cache()->getData('translation', '\\Nick\\' . $translationExtension . '\\' . $translationExtension);
  }

  /**
   * Returns cached Config object
   *
   * @return Config
   */
  public static function Config() {
    return self::Cache()->getData('config', '\\Nick\\Config\\Config');
  }

  /**
   * @return CacheInterface
   */
  public static function Cache() {
    global $cache;
    return $cache ?? Core::getCacheClass();
  }

  /**
   * Returns cached ExtensionManager object
   *
   * @return ExtensionManager
   */
  public static function ExtensionManager() {
    return self::Cache()->getData('extension.manager', '\\Nick\\ExtensionManager\\ExtensionManager');
  }

  /**
   * Returns cached LanguageManager object
   *
   * @return LanguageManager
   */
  public static function LanguageManager() {
    return self::Cache()->getData('language.manager', '\\Nick\\Language\\LanguageManager');
  }

  /**
   * Returns cached Language object
   *
   * @return Language
   */
  public static function Language() {
    return self::Cache()->getData('language', '\\Nick\\Language\\Language');
  }

  /**
   * Returns cached Theme object
   *
   * @return Theme
   */
  public static function Theme() {
    return self::Cache()->getData('theme', '\\Nick\\Theme');
  }

  /**
   * Returns cached Database object
   *
   * @return Database
   */
  public static function Database() {
    return self::Cache()->getData('database', '\\Nick\\Database\\Database');
  }

  /**
   * Bootstraps Nick
   */
  public static function Bootstrap() {
    $core = new Core();
    $core->setSystemSpecifics();
    self::MatterManager()->createMatters();
    self::ExtensionManager()->installExtensions();

    try {
      $logger = new Logger();
      $pageObject = self::PageManager()->getPageObject($_GET['p'] ?? 'dashboard', $_GET);
      $menus = Nick::Manifest('menu')
        ->fields(['id', 'title', 'description', 'route', 'type', 'parent'])
        ->condition('status', 1)
        ->order('structure', 'ASC')
        ->result();
      foreach ($menus as $key => $menu) {
        $menus[$key]['route'] = explode('.', $menus[$key]['route']);
        $menuObject = new Menu();
        $children = $menuObject->loadByProperties(['parent' => $menus[$key]['id']]);
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
      $headerVariables = [];
      $headerVariables['menu'] = $menus;
      $headerVariables['logs'] = ['render' => $logger->render()];
      $headerVariables['current_route'] = [
        'route' => Url::getCurrentRoute(),
        'page' => $_GET['p'] ?? NULL,
        'type' => $_GET['t'] ?? NULL,
        'id' => $_GET['id'] ?? NULL,
      ];
      if ($pageObject instanceof PageInterface) {
        $headerVariables['page'] = [
          'id' => $_GET['p'] ?? 'dashboard',
          'type' => $pageObject->get('type'),
          'title' => $pageObject->get('title'),
          'summary' => $pageObject->get('summary'),
          'cacheclear_uri' => Url::fromRoute(
            [
              'cache',
              'clear_all'
            ],
            [
              'data[p]' => $_GET['p'] ?? NULL,
              'data[t]' => $_GET['t'] ?? NULL,
              'data[id]' => $_GET['id'] ?? NULL,
            ],
          ),
        ];
      }
      $header = self::PageManager()->getPageRender('header', $headerVariables);
      $page = self::PageManager()->getPageRender($_GET['p'] ?? 'dashboard', $_GET);
      $footer = self::PageManager()->getPageRender('footer');

      echo $header ?? NULL;
      echo $page ?? NULL;
      echo $footer ?? NULL;
    } catch (Exception $exception) {
      self::Logger()->add('Could not render Nick!' . PHP_EOL . $exception->getMessage(), Logger::TYPE_FAILURE, 'Bootstrap');
    }
  }

  /**
   * Returns uncached MatterManager object.
   *
   * @return MatterManager
   */
  public static function MatterManager() {
    return new MatterManager();
  }

  /**
   * Returns uncached PageManager object
   *
   * @return PageManager
   */
  public static function PageManager() {
    return new PageManager();
  }

  /**
   * Returns non-cached Manifest object
   *
   * @param string $type
   *
   * @return Manifest
   */
  public static function Manifest(string $type) {
    return new Manifest($type);
  }

  /**
   * Returns cached Logger object
   *
   * @return Logger
   */
  public static function Logger() {
    return self::Cache()->getData('logger', '\\Nick\\Logger');
  }

}