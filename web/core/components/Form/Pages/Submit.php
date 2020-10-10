<?php

namespace Nick\Form\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Submit
 *
 * @package Nick\Form\Pages
 */
class Submit extends Page {

  /**
   * View constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'form.submit',
      'title' => $this->translate('Form submit'),
      'summary' => $this->translate('The view page of an article.'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.form.submit',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    d($_POST);
    d(unserialize(htmlspecialchars_decode($_POST['form-array'])));

    return NULL;
  }

}
