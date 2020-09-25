<?php

namespace Nick\Matter;

use Nick\Renderer;

/**
 * Class MatterRenderer
 *
 * @package Nick\Matter
 */
class MatterRenderer {

  /** @var MatterInterface $matter */
  protected MatterInterface $matter;

  /** @var Renderer $renderer */
  protected Renderer $renderer;

  /**
   * MatterRenderer constructor.
   *
   * @param MatterInterface $matter
   */
  public function __construct(MatterInterface $matter) {
    $this->setMatter($matter);
    $this->setRenderer();
  }

  /**
   * Returns Matter object
   *
   * @return MatterInterface
   */
  protected function getMatter(): MatterInterface {
    return $this->matter;
  }

  /**
   * Sets Matter object
   *
   * @param MatterInterface $matter
   *
   * @return self
   */
  protected function setMatter(MatterInterface $matter): self {
    $this->matter = $matter;
    return $this;
  }

  /**
   * Returns Renderer object
   *
   * @return Renderer
   */
  protected function getRenderer() {
    return $this->renderer;
  }

  /**
   * Sets Renderer object
   *
   * @return $this
   */
  protected function setRenderer(): self {
    $this->renderer = new Renderer();
    return $this;
  }

  /**
   * Returns rendered Matter object
   *
   * @param array $variables
   *
   * @return string|NULL
   */
  public function render($variables = []): ?string {
    $matter = $this->getMatter();
    $values = $matter->getValues();
    $values = array_merge($values, $variables);
    return $this
      ->getRenderer()
      ->setType('matter')
      ->setTemplate($matter->getType())
      ->render($values);
  }

}