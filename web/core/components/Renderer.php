<?php

namespace Nick;

use Exception;
use Nick;
use Nick\Event\Event;
use Nick\Person\Person;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;
use Twig\Extension\DebugExtension;

/**
 * Class Renderer
 *
 * @package Nick
 */
class Renderer extends Settings {

  /** @var string|null $type */
  protected $type;

  /** @var string $template */
  protected string $template;

  /** @var FilesystemLoader $loader */
  protected FilesystemLoader $loader;

  /** @var Environment $twig */
  protected Environment $twig;

  /** @var string $theme_folder */
  protected string $theme_folder;

  /**
   * Renderer constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->setThemeFolder(Nick::Theme()->getThemeFolder());
  }

  /**
   * @return FilesystemLoader
   */
  protected function getLoader() {
    return $this->loader;
  }

  /**
   * @return Environment
   */
  protected function getTwig() {
    return $this->twig;
  }

  /**
   * @return string
   */
  protected function getThemeFolder() {
    return $this->theme_folder;
  }

  /**
   * @param string $theme_folder
   */
  protected function setThemeFolder($theme_folder) {
    $this->theme_folder = $theme_folder;
  }

  /**
   * @param string|NULL $type
   *
   * @return Renderer|NULL
   */
  public function setType($type = NULL) {
    $this->type = $type;
    $path = is_null($type) ? $this->getThemeFolder() :
      $this->getThemeFolder() . $type;

    if (!is_dir($path)) {
      if (StringManipulation::contains($type, 'extension')) {
        $path = 'extensions/' . StringManipulation::replace($type, 'extension.', '') . '/templates';
      } else {
        Nick::Logger()->add('[setType]: Folder not found.', Logger::TYPE_WARNING, 'Renderer');
        return NULL;
      }
    }

    $this->loader = new FilesystemLoader($path);
    $this->twig = new Environment($this->getLoader(), ['debug' => $this->getSetting('twig_debugging')]);
    if ($this->getSetting('twig_debugging')) {
      $this->twig->addExtension(new DebugExtension());
    }

    return $this;
  }

  /**
   * @return string
   */
  protected function getType() {
    return $this->type;
  }

  /**
   * @param string $template
   *
   * @return Renderer
   */
  public function setTemplate(string $template) {
    $this->template = $template;
    return $this;
  }

  /**
   * @return bool|TemplateWrapper
   */
  protected function getTemplate() {
    try {
      return $this->getTwig()->load($this->template . '.html.twig');
    } catch (LoaderError $e) {
      Nick::Logger()->add($e->getMessage(), Logger::TYPE_FAILURE, 'Renderer');
    } catch (RuntimeError $e) {
      Nick::Logger()->add($e->getMessage(), Logger::TYPE_FAILURE, 'Renderer');
    } catch (SyntaxError $e) {
      Nick::Logger()->add($e->getMessage(), Logger::TYPE_FAILURE, 'Renderer');
    }
    return FALSE;
  }

  /**
   * @param array       $variables
   * @param string|NULL $view_mode
   *
   * @return string|NULL
   */
  public function render(array $variables = [], $view_mode = NULL): ?string {
    $event = new Event('preRender');
    $event->fire($variables, [$view_mode]);

    $template = $this->getTemplate();
    if (!$template instanceof TemplateWrapper) {
      return FALSE;
    }
    $render_settings = $this->settings;
    unset($render_settings['database']);
    $variables = $variables + [
        'settings' => $render_settings,
        'active_user' => Person::getCurrentPerson(),
        'site' => [
          'name' => Nick::Config()->get('site.name') ?? 'Nick',
          'version' => Nick::Cache()->getData('NICK_VERSION') . '.'
            . Nick::Cache()->getData('NICK_VERSION_RELEASE') . ' '
            . Nick::Cache()->getData('NICK_VERSION_STATUS'),
        ],
        'theme' => [
          'location' => 'themes/' . Nick::Theme()->getTheme('admin'),
        ],
      ];

    return $template->render($variables) ?? NULL;
  }

}