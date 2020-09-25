<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Form\FormBuilder;
use Nick\Url;

/**
 * Class Logs
 *
 * @package Nick\Page
 */
class Logs extends Page {

  /**
   * Config constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->setParameters([
      'id' => 'logs',
      'title' => $this->translate('Logs'),
      'summary' => $this->translate('Shows recent logs'),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.logs',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Page\\Pages\\Logs',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);

    if (isset($parameters['t'])) {
      if ($parameters['t'] == 'clear') {
        Nick::Logger()->clear();
        header('Location: ' . Url::fromRoute('logs'));
      }
    }

    $logs = Nick::Logger()->getLogs(TRUE);
    return Nick::Renderer()
      ->setType()
      ->setTemplate('logs')
      ->render([
        'logs' => $logs,
        'count' => count($logs),
      ]);
  }

}