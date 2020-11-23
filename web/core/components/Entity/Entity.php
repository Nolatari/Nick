<?php

namespace Nick\Entity;

use Exception;
use Nick;
use Nick\Database\Database;
use Nick\Database\Result;
use Nick\Event\Event;
use Nick\Logger;
use Nick\Person\Entity\Person;
use Nick\StringManipulation;

/**
 * Class Entity
 *
 * @package Nick\Entity
 */
class Entity implements EntityInterface {

  /** Constants for status values */
  const UNPUBLISHED = 0;
  const PUBLISHED = 1;
  /** Constants for cardinality */
  const CARDINALITY_UNLIMITED = 0;
  const CARDINALITY_DEFAULT = 25;

  /** @var string $type */
  protected string $type;

  /** @var array|NULL $values */
  protected ?array $values;

  /** @var array|bool $fields */
  protected $fields = FALSE;

  /**
   * @param int    $id
   * @param bool   $massage
   *
   * @return EntityInterface|bool
   *
   */
  public static function load(int $id, bool $massage = FALSE) {
    $entityClassName = static::class;
    /** @var EntityInterface $entity */
    $entity = new $entityClassName;

    if ($id === 0 && $entity->getType() !== 'person') {
      return FALSE;
    }

    return \Nick::EntityManager()->loadByProperties(
      ['type' => $entity->getType(), 'id' => $id],
      FALSE,
      $massage
    );
  }

  /**
   * Loads all entities of entity type
   *
   * @param bool $massage
   *
   * @return array|bool
   */
  public static function loadMultiple(bool $massage = FALSE) {
    $entityClassName = static::class;
    /** @var EntityInterface $entity */
    $entity = new $entityClassName;
    return \Nick::EntityManager()->loadByProperties(['type' => $entity->getType()], $massage);
  }

  /**
   * Creates the entity type in database
   *
   * @return bool
   */
  public static function create() {
    $entityClassName = static::class;
    /** @var EntityInterface $entity */
    $entity = new $entityClassName;
    $type = $entity->getType();
    if (EntityManager::entityInstalled($type)) {
      return FALSE;
    }
    $database = \Nick::Database();
    $results = [];
    $auto_increment = FALSE;
    $fields_storage = Entity::fields() + static::initialFields();
    $fields = '';
    foreach ($fields_storage as $field => $options) {
      if (!in_array(strtoupper($options['type']), Database::getFieldTypes())) {
        \Nick::Logger()->add('Field type /' . $options['type'] . '/ does not comply to possible field types.', Logger::TYPE_WARNING, 'Entity');
      }
      if ($fields !== '') {
        $fields .= ',' . PHP_EOL;
      }
      $fields .= '  ' . Database::createFieldQuery($field, $options);

      if (isset($options['auto_increment']) && $options['auto_increment'] !== FALSE) {
        $auto_increment = $field;
      }
    }

    $fullClassName = StringManipulation::explode($entityClassName, '\\');
    $className = array_pop($fullClassName);
    $database->insert('entity_storage')
      ->values([
        'type' => $type,
        'label' => $className,
        'description' => 'This item creates the ' . $className . ' entity.',
        'fields' => serialize(static::initialFields()),
      ])
      ->execute();
    $query = $database->query('CREATE TABLE `entity__' . $type . '` (
' . $fields . '
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    if (!$results[] = $query) {
      \Nick::Logger()->add('Something went wrong while querying the create function.', Logger::TYPE_WARNING, 'Entity');
    }

    if ($auto_increment !== FALSE) {
      $add_primary_key = $database->query('ALTER TABLE `entity__' . $type . '`
ADD PRIMARY KEY (`' . $auto_increment . '`);');
      $results[] = $add_primary_key;
      $add_auto_increment = $database->query('ALTER TABLE `entity__' . $type . '`
  MODIFY ' . Database::createFieldQuery($auto_increment, $fields_storage[$auto_increment]) . ' AUTO_INCREMENT;');
      $results[] = $add_auto_increment;
    }

    foreach ($results as $result) {
      if (!$result) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * @param array $entity
   * @param bool  $massage
   *
   * @return mixed
   */
  public function massageProperties(array $entity, $massage = TRUE) {
    if ($massage) {
      foreach ($entity as $ci_key => $ci_value) {
        if ($ci_key === 'status') {
          $entity[$ci_key] = static::intToStatus($ci_value);
          continue;
        } elseif ($ci_key === 'id' || !isset(static::fields()[$ci_key]['class'])) {
          continue;
        }
        $className = static::fields()[$ci_key]['class'];
        $class = new $className;
        $entity[$ci_key] = $class::load($ci_value);
      }
    }
    $entityClass = EntityManager::getEntityClassFromType($this->getType());
    if ($entityClass !== FALSE) {
      return new $entityClass($entity);
    }

    return FALSE;
  }

  /**
   * @param $int
   *
   * @return string
   */
  public static function intToStatus($int) {
    switch ($int) {
      case 0:
        return translate('Unpublished');
      case 1:
        return translate('Published');
      default:
        return translate('Undefined');
    }
  }

  /**
   * {@inheritDoc}
   */
  public static function fields() {
    return [
      'id' => [
        'type' => 'int',
        'length' => 25,
        'unique' => TRUE,
      ],
      'owner' => [
        'type' => 'int',
        'length' => 25,
        'default_value' => Person::getCurrentPerson(),
        'class' => '\\Nick\\Person\\Entity\\Person',
      ],
      'status' => [
        'type' => 'tinyint',
        'length' => 1,
        'default_value' => self::UNPUBLISHED,
        'form' => [
          'title' => 'Status',
          'type' => 'select',
          'name' => 'status',
          'options' => [
            self::PUBLISHED => 'Published',
            self::UNPUBLISHED => 'Unpublished',
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Sets entity type
   *
   * @param $type
   *
   * @return Entity
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * Adds options to default field options
   *
   * @param array  $default_fields
   * @param string $field
   * @param array  $options
   *
   * @return array
   */
  public function addToDefaultOptions(array $default_fields, string $field, $options = []) {
    $fields = $default_fields;
    foreach ($options as $key => $value) {
      if ($fields[$field]['form']['options']['type'] !== 'select') {
        continue;
      }
      $fields[$field]['form']['options'][$key] = $value;
    }

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function getAllFields() {
    if (!$this->getType()) {
      return FALSE;
    }

    $fields_storage = \Nick::Database()
      ->select('entity_storage')
      ->fields(NULL, ['fields'])
      ->condition('type', $this->getType());
    /** @var Result $fields_result */
    if (!$fields_result = $fields_storage->execute()) {
      return FALSE;
    }

    $fields = $fields_result->fetchAllAssoc();
    return self::fields() + unserialize($fields[0]['fields']);
  }

  /**
   * {@inheritDoc}
   */
  public function getStorage($type, $values = []) {
    $entityClass = EntityManager::getEntityClassFromType($type);
    if (!$entityClass) {
      return FALSE;
    }
    $entityClass = new $entityClass($values);
    return $entityClass;
  }

  /**
   * @param array|null $values
   */
  protected function setValues(?array $values) {
    $this->values = $values;
  }

  /**
   * {@inheritDoc}
   */
  public function setValue(string $key, $value) {
    if (!isset($this->values[$key])) {
      return FALSE;
    }
    $this->values[$key] = $value;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function validate() {
    // @TODO!!!
  }

  /**
   * {@inheritDoc}
   */
  public function save() {
    // Fire presave event
    \Nick::Event('EntityPreSave')
      ->fire($this);

    $table = 'entity__' . $this->type;
    // Check if item exists, update existing item or insert new item.
    if ($this->id() !== NULL) {
      $check = \Nick::Database()->select($table)
        ->condition('id', $this->id());
      if (!$result = $check->execute()) {
        \Nick::Logger()->add('Something went wrong trying to execute query', Logger::TYPE_FAILURE, 'Entity');
        return FALSE;
      }
      if (!$result->fetchAllAssoc()) {
        $check_existing = \Nick::Database()->select($table);
        foreach ($this->getUniqueFields() as $field) {
          $check_existing->condition($field, $this->getValue($field));
        }
        if (!$result_existing = $check_existing->execute()) {
          \Nick::Logger()->add('Something went wrong trying to execute Entity Save query', Logger::TYPE_FAILURE, 'Entity');
          return FALSE;
        }
        if ($result = $result_existing->fetchAllAssoc()) {
          \Nick::Logger()->add('Some field already exists during Entity Save Query.', Logger::TYPE_FAILURE, 'Entity');
          return FALSE;
        }

        $values = ['id' => 0] + $this->massageValueArray();
        $query = \Nick::Database()->insert($table)
          ->values($values);
      } else {
        $values = $this->getValues();
        foreach ($values as $key => $value) {
          if ($value === FALSE) {
            $values[$key] = 0;
          }
        }
        // ID is not supposed to be changed manually!
        unset($values['id']);
        $query = \Nick::Database()->update($table)
          ->condition('id', $this->id())
          ->values($values);
      }
    } else {
      if (!$this->addEntity($this->getType())) {
        \Nick::Logger()->add('Something went wrong trying to execute Entity Save query', Logger::TYPE_FAILURE, 'Entity');
        return FALSE;
      }
      $id = \Nick::Database()->select('INFORMATION_SCHEMA.TABLES')
        ->fields(NULL, ['AUTO_INCREMENT'])
        ->condition('TABLE_SCHEMA', \Nick::Database()->getDatabaseName())
        ->condition('TABLE_NAME', 'entity');
      if (!$id_result = $id->execute()) {
        \Nick::Logger()->add('Something went wrong trying to execute Entity Save query', Logger::TYPE_FAILURE, 'Entity');
        return FALSE;
      }
      $result = $id_result->fetchAllAssoc();
      // Autoincrement ID (next ID) => current ID
      $id = reset($result)['AUTO_INCREMENT'] - 1;
      $values = ['id' => $id] + $this->massageValueArray();
      $query = \Nick::Database()->insert($table)
        ->values($values);
    }

    if (!$query->execute()) {
      return FALSE;
    }

    // Fire postsave event
    \Nick::Event('EntityPostSave')
      ->fire($this);
    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function id() {
    return $this->getValue('id');
  }

  /**
   * {@inheritDoc}
   */
  public function getValue(string $key) {
    return $this->values[$key] ?? NULL;
  }

  /**
   * @return array
   */
  protected function getUniqueFields() {
    $fields = Entity::fields() + static::initialFields();
    $unique_fields = [];
    foreach ($fields as $field => $options) {
      if (isset($options['unique']) && $options['unique']) {
        $unique_fields[] = $field;
      }
    }
    return $unique_fields;
  }

  /**
   * Massages value array to comply with fields order
   *
   * @return array
   */
  protected function massageValueArray() {
    $values = $this->values;
    $fields = Entity::fields() + static::initialFields();
    $new_value_array = [];
    foreach ($fields as $field => $options) {
      if ($field === 'id') {
        continue;
      }
      $new_value_array[$field] = $values[$field] ?? 0;
    }
    return $new_value_array;
  }

  /**
   * @return array|NULL
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * Default function to return title, to be overwritten in entities
   *
   * @return null
   */
  public function getTitle() {
    return NULL;
  }

  /**
   * @param string $type
   *
   * @return bool
   */
  protected function addEntity(string $type) {
    $query = \Nick::Database()->insert('entity')
      ->values([
        'id' => 0,
        'type' => $type,
      ]);
    return $query->execute();
  }

  /**
   * {@inheritDoc}
   */
  public function delete() {
    if ($this->id() == NULL) {
      \Nick::Logger()->add('Cannot remove Entity without ID', Logger::TYPE_FAILURE, 'Entity');
      return FALSE;
    }

    // Fire predelete event
    \Nick::Event('EntityPreDelete')
      ->fire($this);

    $query = \Nick::Database()
      ->delete('entity__' . $this->getType())
      ->condition('id', $this->id());
    $query_entity = \Nick::Database()
      ->delete('entity')
      ->condition('id', $this->id());

    if (!$query->execute() || !$query_entity->execute()) {
      \Nick::Logger()->add('Something went wrong trying to remove Entity ' . $this->getType() . ' [' . $this->id() . ']', Logger::TYPE_FAILURE, 'Entity');
      return FALSE;
    }

    // Fire postdelete event
    \Nick::Event('EntityPostDelete')
      ->fire($this);
    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function owner() {
    return Person::load($this->getValue('owner'));
  }
}