<?php

namespace Nick\Manifest;

use Nick\Renderer;
use Nick\Translation\StringTranslation;

/**
 * Class EntityRenderer
 *
 * @package Nick\Entity
 */
class ManifestRenderer {
  use StringTranslation;

  /** @var ManifestInterface $manifest */
  protected ManifestInterface $manifest;

  /** @var Renderer $renderer */
  protected Renderer $renderer;

  /** @var string $viewMode */
  protected string $viewMode = 'table';

  /** @var array $hiddenFields */
  protected array $hiddenFields = [];

  /** @var array $noLinkFields */
  protected array $noLinkFields = [];

  /** @var string|bool $actionLinks */
  protected $actionLinks = FALSE;

  /**
   * EntityRenderer constructor.
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
   * Returns Entity object
   *
   * @return ManifestInterface
   */
  protected function getManifest(): ManifestInterface {
    return $this->manifest;
  }

  /**
   * Sets Entity object
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
   * Visually hides field, data is still available for other fields to manipulate
   *
   * @param string $field
   *
   * @return $this
   */
  public function hideField(string $field): self {
    $this->hiddenFields[$field] = TRUE;
    return $this;
  }

  /**
   * Won't show a link for this field if a link is available.
   * Think about Owner, Title, ...
   *
   * @param string $field
   *
   * @return $this
   */
  public function noLink(string $field): self {
    $this->noLinkFields[$field] = TRUE;
    return $this;
  }

  /**
   *
   *
   * @param string $entityType
   *
   * @return $this
   */
  public function addActionLinks(string $entityType): self {
    $this->actionLinks = $entityType;
    return $this;
  }

  /**
   * Returns rendered Entity object
   *
   * @param bool $massage
   *
   * @return string|NULL
   */
  public function render(bool $massage = FALSE): ?string {
    $viewMode = $this->getViewMode();
    $manifest = $this->getManifest();

    $fields = $manifest->getFields();
    $results = $manifest->result($massage);

    if ($this->actionLinks !== FALSE) {
      $fields[] = 'edit-link';
      $fields[] = 'delete-link';
      foreach ($results as &$result) {
        //$result['edit-link'] = '<a href="' . Url::fromRoute([$this->actionLinks, 'edit', $result['id']]) . '">' . $this->translate('Edit') . '</a>';
        //$result['delete-link'] = '<a href="' . Url::fromRoute([$this->actionLinks, 'delete', $result['id']]) . '">' . $this->translate('Delete') . '</a>';
      }
    }

    d($results);

    return $this->getRenderer()
      ->setType('core.Manifest')
      ->setTemplate($viewMode)
      ->render([
        'fields' => $fields,
        'values' => $results,
        'hidden' => $this->hiddenFields,
      ]);
  }

}