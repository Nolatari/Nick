<?php

namespace Nick\Manifest;

use Nick\Renderer;

/**
 * Class MatterRenderer
 *
 * @package Nick\Matter
 */
class ManifestRenderer {

  /** @var ManifestInterface $manifest */
  protected ManifestInterface $manifest;

  /** @var Renderer $renderer */
  protected Renderer $renderer;

  /** @var string $viewMode */
  protected string $viewMode = 'table';

  /**
   * MatterRenderer constructor.
   *
   * @param ManifestInterface $manifest
   */
  public function __construct(ManifestInterface $manifest) {
    $this->setManifest($manifest);
    $this->setRenderer();
  }

  /**
   * @return string
   */
  protected function getViewMode(): string {
    return $this->viewMode;
  }

  /**
   * @param string $viewMode
   *
   * @return ManifestRenderer
   */
  public function setViewMode(string $viewMode): self {
    $this->viewMode = $viewMode;
    return $this;
  }

  /**
   * Returns Matter object
   *
   * @return ManifestInterface
   */
  protected function getManifest(): ManifestInterface {
    return $this->manifest;
  }

  /**
   * Sets Matter object
   *
   * @param ManifestInterface $manifest
   *
   * @return self
   */
  protected function setManifest(ManifestInterface $manifest): self {
    $this->manifest = $manifest;
    return $this;
  }

  /**
   * Returns Renderer object
   *
   * @return Renderer
   */
  protected function getRenderer(): Renderer {
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
   * @return string|NULL
   */
  public function render(): ?string {
    $viewMode = $this->getViewMode();
    $manifest = $this->getManifest();

    return $this->getRenderer()
      ->setType('core.Manifest')
      ->setTemplate($viewMode)
      ->render([
        'fields' => $manifest->getFields(),
        'values' => $manifest->result(),
      ]);
  }

}