<?php

namespace Nick;

use Nick;
use Nick\Person\Entity\Person;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Class Renderer
 *
 * @package Nick
 */
class Renderer {

  /** @var string|null $type */
  protected ?string $type;

  /** @var string|null $path */
  protected ?string $path;

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
    $this->setThemeFolder(\Nick::Theme()->getThemeFolder());
  }

  /**
   * @return FilesystemLoader
   */
  protected function getLoader() {
    return $this->loader;
  }

  /**
   * @param FilesystemLoader $loader
   *
   * @return self
   */
  protected function setLoader(FilesystemLoader $loader) {
    $this->loader = $loader;
    return $this;
  }

  /**
   * @return Environment
   */
  protected function getTwig() {
    return $this->twig;
  }

  /**
   * @param Environment $twig
   *
   * @return self
   */
  protected function setTwig(Environment $twig) {
    $this->twig = $twig;
    return $this;
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
  protected function setThemeFolder(string $theme_folder) {
    $this->theme_folder = $theme_folder;
  }

  /**
   * @param string|NULL $type
   *
   * @param bool        $skip
   *
   * @return Renderer|NULL
   */
  public function setType($type = NULL, $skip = FALSE) {
    $this->type = $type;
    $path = is_null($type) ? $this->getThemeFolder() :
      $this->getThemeFolder() . StringManipulation::replace($type, ['core.', 'extension.'], '') . '/';

    if (!is_dir($path) || $skip) {
      if (StringManipulation::contains($type, 'extension')) {
        $path = 'extensions/' . StringManipulation::replace($type, 'extension.', '') . '/templates/';
        if (!is_dir($path)) {
          $path = 'core/extensions/' . StringManipulation::replace($type, 'extension.', '') . '/templates/';
        }
      } elseif (StringManipulation::contains($type, 'core')) {
        $path = 'core/components/' . StringManipulation::replace($type, 'core.', '') . '/templates/';
      } else {
        \Nick::Logger()->add('[setType]: Folder not found.', Logger::TYPE_WARNING, 'Renderer');
        return NULL;
      }
    }
    $this->path = $path;

    $this->setLoader(new FilesystemLoader($path));
    $this->setTwig(new Environment($this->getLoader(), ['debug' => Settings::get('twig_debugging')]));
    if (Settings::get('twig_debugging')) {
      $this->twig->addExtension(new DebugExtension());
    }
    $extensions = new TwigExtensions();
    $this->twig->addExtension($extensions);

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
    if (!is_file($this->path . $template . '.html.twig')) {
      $this->setType($this->type, TRUE);
    }
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
      \Nick::Logger()->add($e->getMessage(), Logger::TYPE_FAILURE, 'Renderer');
    } catch (RuntimeError $e) {
      \Nick::Logger()->add($e->getMessage(), Logger::TYPE_FAILURE, 'Renderer');
    } catch (SyntaxError $e) {
      \Nick::Logger()->add($e->getMessage(), Logger::TYPE_FAILURE, 'Renderer');
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
    \Nick::Event('preRender')
      ->fire($variables, [$view_mode]);

    $template = $this->getTemplate();
    if (!$template instanceof TemplateWrapper) {
      return FALSE;
    }
    $render_settings = Settings::getAll();
    unset($render_settings['database']);
    $variables = $variables + [
        'settings' => $render_settings,
        'active_user' => Person::getCurrentPerson(),
        'site' => [
          'name' => \Nick::Config()->get('site.name') ?? 'Nick',
          'version' => \Nick::Cache()->getData('NICK_VERSION') . '.'
            . \Nick::Cache()->getData('NICK_VERSION_RELEASE') . '.'
            . \Nick::Cache()->getData('NICK_VERSION_RELEASE_MINOR') . ' '
            . \Nick::Cache()->getData('NICK_VERSION_STATUS'),
        ],
        'theme' => [
          'location' => Settings::get('root.web.url') . '/themes/' . \Nick::Theme()->getTheme('admin'),
        ],
      ];

    return $template->render($variables) ?? NULL;
  }

}