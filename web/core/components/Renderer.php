<?php

namespace Nick;

use Exception;
use Nick;
use Nick\Person\Person;
use Nick\Person\PersonInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Class Renderer
 *
 * @package Nick
 */
class Renderer extends Settings {

  /** @var string $type */
  protected $type;

  /** @var string $template */
  protected $template;

  /** @var FilesystemLoader $loader */
  protected $loader;

  /** @var Environment $twig */
  protected $twig;

  /** @var string $theme_folder */
  protected $theme_folder;

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

    if (!is_dir($this->getThemeFolder() . $type)) {
      Nick::Logger()->add('[Renderer][setType]: Folder not found.', Logger::TYPE_WARNING, 'Renderer');
      return NULL;
    }

    $path = is_null($type) ? $this->getThemeFolder() :
      $this->getThemeFolder() . $type;
    $this->loader = new FilesystemLoader($path);
    $this->twig = new Environment($this->getLoader());

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
   * @return TemplateWrapper|Exception
   */
  protected function getTemplate() {
    try {
      return $this->getTwig()->load($this->template . '.html.twig');
    } catch (LoaderError $e) {
      return new Exception('Something went wrong loading the template [' . $this->template . '.html.twig]');
    } catch (RuntimeError $e) {
      return new Exception('Something went wrong running the template [' . $this->template . '.html.twig]');
    } catch (SyntaxError $e) {
      return new Exception('Syntax error when loading the template [' . $this->template . '.html.twig]');
    }
  }

  /**
   * @param array $variables
   *
   * @return string|NULL
   */
  public function render(array $variables = [], $view_mode = NULL) {
    $template = $this->getTemplate();
    if (!$template instanceof TemplateWrapper) {
      return FALSE;
    }
    $render_settings = $this->settings;
    unset($render_settings['database']);
    $variables = $variables + [
        'settings' => $this->settings,
        'active_user' => Person::getCurrentUser()
      ];
    if ($this->template === 'page') {
      if (!$people = Person::loadMultiple()) {
        Nick::Logger()->add('No people yet.', 'Renderer', Logger::TYPE_FAILURE);
        return FALSE;
      }
      $people = array_map(function (PersonInterface $person) {
        return $person->render();
      }, $people);
      $logger = new Logger();
      $variables = $variables + [
          'header' => $this->setType()->setTemplate('header')->render([
            'people' => $people,
            'active_user' => Person::getCurrentUser(),
            'logs' => $logger->render()
          ]),
          'footer' => $this->setType()->setTemplate('footer')->render(),
        ];
    }
    return $template->render($variables) ?? NULL;
  }

}