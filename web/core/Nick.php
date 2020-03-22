<?php

use Nick\ExtensionManager;
use Nick\Page\PageInterface;
use Nick\Cache\CacheInterface;
use Nick\Config;
use Nick\Core;
use Nick\Database\Database;
use Nick\Form\FormBuilder;
use Nick\Form\FormElement;
use Nick\Matter\MatterManager;
use Nick\Language\LanguageManager;
use Nick\Logger;
use Nick\Manifest\Manifest;
use Nick\Matter\Matter;
use Nick\Matter\MatterInterface;
use Nick\Page\PageManager;
use Nick\Renderer;
use Nick\Theme;
use Nick\Translation\TranslationInterface;

/**
 * Nick helper class.
 */
class Nick {

  /**
   * @return CacheInterface
   */
  public static function Cache() {
    global $cache;
    return $cache ?? Core::getCacheClass();
  }

  /**
   * Returns non-cached Matter object
   *
   * @return Matter
   */
  public static function Matter() {
    return new Matter();
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
   * Returns non-cached Manifest object
   *
   * @param string $type
   *
   * @return Manifest
   */
  public static function Manifest($type) {
    return new Manifest($type);
  }

  /**
   * Returns non-cached FormBuilder object
   *
   * @param MatterInterface $matter
   *
   * @return FormBuilder
   */
  public static function FormBuilder(MatterInterface $matter) {
    return new FormBuilder($matter);
  }

  /**
   * Returns non-cached FormElement object
   *
   * @return FormElement
   */
  public static function FormElement() {
    return new FormElement();
  }

  /**
   * Returns cached Logger object
   *
   * @return Logger
   */
  public static function Logger() {
    return self::Cache()->getData('logger', '\\Nick\\Logger');
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
   * Returns cached Config object
   *
   * @return Config
   */
  public static function Config() {
    return self::Cache()->getData('config', '\\Nick\\Config');
  }

  /**
   * Returns cached Translation object
   *
   * @return TranslationInterface
   */
  public static function Translation() {
    $translationExtension = self::Config()->get('translation')['extension'];
    if (!ExtensionManager::extensionInstalled($translationExtension)) {
      $translationExtension = 'Translation';
    }
    return self::Cache()->getData('translation', '\\Nick\\' . $translationExtension . '\\' . $translationExtension);
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
   * Returns uncached PageManager object
   *
   * @return PageManager
   */
  public static function PageManager() {
    return new PageManager();
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

    try {
      $page = self::PageManager()->getPage($_GET['p'] ?? 'dashboard', $_GET);
      $pageObject = self::PageManager()->getPageObject($_GET['p'] ?? 'dashboard', $_GET);
      $headerVariables = [];
      if ($pageObject instanceof PageInterface) {
        $headerVariables = [
          'page' => [
            'id' => $_GET['p'] ?? 'dashboard',
            'title' => $pageObject->get('title'),
            'summary' => $pageObject->get('summary'),
          ],
        ];
      }
      $header = self::PageManager()->getPage('header', $headerVariables);
      $footer = self::PageManager()->getPage('footer');

      echo $header ?? NULL;
      echo $page ?? NULL;
      echo $footer ?? NULL;
    } catch (Exception $exception) {
      self::Logger()->add('Could not bootstrap Nick!' . PHP_EOL . $exception, Logger::TYPE_FAILURE, 'Bootstrap');
    }
  }

}