<?php

namespace Nick\Form;

use Nick;
use Nick\Event\Event;
use Nick\Logger;
use Nick\Matter\MatterInterface;
use Nick\Translation\StringTranslation;

/**
 * Class Form
 *
 * @package Nick\Form
 */
class Form extends FormBuilder implements FormInterface {
  use StringTranslation;

  /** @var MatterInterface|null $matter */
  protected $matter = NULL;

  /** @var array $values */
  protected array $values = [];

  /** @var array $fields */
  protected array $fields = [];

  /** @var string $id */
  protected string $id;

  /**
   * Form constructor.
   *
   * @param MatterInterface|null $matter
   */
  public function __construct(MatterInterface $matter = NULL) {
    if (!is_null($matter)) {
      $this->matter = $matter;
      $this->setFields($matter::fields() + $matter::initialFields());
      if ($matter->getValues() !== []) {
        $this->setId($matter->id() . '-edit-form');
        $this->setValues($matter->getValues() ?: []);
      } else {
        $this->setId($matter->id() . '-add-form');
      }
    }
  }

  /**
   * Fire events and submit handler
   *
   * @param array  $form
   * @param string $formId
   *
   * @return bool
   */
  public function submit(array &$form, string $formId): bool {
    // Fire FormPreSubmitAlter event
    $preSubmitEvent = new Event('FormPreSubmitAlter');
    $preSubmitEvent->fire($form, [$formId]);

    // Fire submit handler
    if (!$handler = $form['submit']['form']['handler']) {
      return FALSE;
    }
    $handlerClass = new $handler[0];

    try {
      // Attempt to call the submit handler
      $handlerClass->{$handler[1]}($form, $_POST);
    } catch(\Exception $e) {
      Nick::Logger()->add($e->getMessage(), Logger::TYPE_ERROR, 'Form Submit');
    }

    // Fire FormPostSubmitAlter event
    $postSubmitEvent = new Event('FormPostSubmitAlter');
    $postSubmitEvent->fire($form, [$formId]);
    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  protected function getFields(): array {
    return $this->fields;
  }

  /**
   * Sets fields array.
   *
   * @param array $values
   *
   * @return self
   */
  public function setFields(array $values): self {
    $this->fields = $values;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  protected function getValue(string $key): ?string {
    return $this->values[$key] ?? NULL;
  }

  /**
   * Returns array of values.
   *
   * @return array
   */
  protected function getValues(): array {
    return $this->values;
  }

  /**
   * Sets value array.
   *
   * @param array $values
   *
   * @return self
   */
  public function setValues(array $values): self {
    $this->values = $values;

    return $this;
  }

  /**
   * Default submit handler does nothing because there is nothing to handle!
   */
  public function submitForm() {

  }

  /**
   * {@inheritDoc}
   */
  protected function getId(): string {
    return $this->id;
  }

  /**
   * Sets form ID.
   *
   * @param string $id
   *
   * @return self
   */
  public function setId(string $id): self {
    $this->id = $id;

    return $this;
  }

  /**
   * Returns content item object.
   *
   * @return MatterInterface|null
   */
  protected function getMatter(): ?MatterInterface {
    return $this->matter;
  }

}