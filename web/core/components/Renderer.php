<?php

namespace Nick;

use Exception;
use Nick\Card\Card;
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
   * @param string|NULL $type
   *
   * @return Renderer|NULL
   */
  public function setType($type = NULL) {
    $this->type = $type;

    if (!is_dir($this->getSetting('theme')['folder'] . '/' . $type)) {
      \Nick::Logger()->add('[Renderer][setType]: Folder not found.', Logger::TYPE_WARNING, 'Renderer');
      return NULL;
    }

    $path = is_null($type) ? $this->getSetting('theme')['folder'] :
      $this->getSetting('theme')['folder'] . '/' . $type;
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
      \Nick::Logger()->add('Something went wrong calling the template!');
      return new Exception('Something bad happened peepoSad');
    } catch (RuntimeError $e) {
      \Nick::Logger()->add('Something went wrong calling the template!');
      return new Exception('Something bad happened peepoSad');
    } catch (SyntaxError $e) {
      \Nick::Logger()->add('Something went wrong calling the template!');
      return new Exception('Something bad happened peepoSad');
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
      $logger = new Logger();
      if (!$users = Person::loadMultiple()) {
        \Nick::Logger()->add('No users yet.', 'Renderer', Logger::TYPE_FAILURE);
        return FALSE;
      }
      $users = array_map(function (PersonInterface $user) {
        return $user->render();
      }, $users);
      $variables = $variables + [
          'header' => $this->setType()->setTemplate('header')->render([
            'users' => $users,
            'active_user' => Person::getCurrentUser(),
            'logs' => $logger->render()
          ]),
          'footer' => $this->setType()->setTemplate('footer')->render(['cards' => Card::loadMultiple()]),
        ];
    }
    return $template->render($variables) ?? NULL;
  }

}