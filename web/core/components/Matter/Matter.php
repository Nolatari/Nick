<?php

namespace Nick\Matter;

use Exception;
use Nick;
use Nick\Core;
use Nick\Database\Database;
use Nick\Database\Result;
use Nick\Events\Event;
use Nick\Logger;
use Nick\Person\Person;

/**
 * Class Matter
 *
 * @package Nick\Matter
 */
class Matter implements MatterInterface {

  /** Constants for status values */
  const UNPUBLISHED = 0;
  const PUBLISHED = 1;
  /** Constants for cardinality */
  const CARDINALITY_UNLIMITED = 0;
  const CARDINALITY_DEFAULT = 25;

  /** @var Database $database */
  protected $database;

  /** @var string $type */
  protected $type;

  /** @var array|NULL $values */
  protected $values;

  /**
   * Card constructor.
   *
   * @param array|NULL $values
   */
  public function __construct($values = NULL) {
    $this->database = \Nick::Database();
    $this->values = $values;
  }

  /**
   * @param int $id
   * @param string $type
   *
   * @return MatterInterface|bool
   */
  protected static function loadMatter(int $id, string $type) {
    if ($id === 0) {
      return FALSE;
    }
    $matterClass = Core::getMatterClassFromType($type);
    $matterClass = new $matterClass;
    return $matterClass->loadByProperties(['id' => $id]);
  }

  /**
   * @param string $type
   *          Type of Matter
   *
   * @return array|bool
   */
  protected static function loadMultipleMatters(string $type) {
    $matterClass = Core::getMatterClassFromType($type);
    $matterClass = new $matterClass;
    return $matterClass->loadByProperties([], TRUE);
  }

  /**
   * @param array $properties
   *          An array of properties your Matter should have
   * @param bool $multiple
   *          If you expect multiple results, set this to TRUE
   *
   * @return bool|array
   *
   * @throws Exception
   */
  public function loadByProperties($properties = [], $multiple = FALSE) {
    if (!$this->getType()) {
      return FALSE;
    }
    $query = $this->database->select('matter__' . $this->getType())
      ->condition('status', 1)
      ->orderBy('id', 'ASC');
    foreach ($properties as $field => $value) {
      $query->condition($field, $value);
    }
    try {
      /** @var Result $result */
      $result = $query->execute();
    } catch (Exception $exception) {
      throw new Exception($exception);
    }
    if (!$results = $result->fetchAllAssoc('id')) {
      return FALSE;
    }

    if (count($results) === 1 && $multiple === FALSE) {
      $matter = reset($results);
      return $this->massageProperties($matter);
    }

    $matters = [];
    foreach ($results as $id => $matter) {
      $matters[] = $this->massageProperties($matter);
    }
    return $matters;
  }

  /**
   * @param array $matter
   *
   * @return mixed
   */
  protected function massageProperties(array $matter) {
    foreach ($matter as $ci_key => $ci_value) {
      if ($ci_key === 'owner' || !isset(static::fields()[$ci_key]['class'])) {
        continue;
      }
      $className = static::fields()[$ci_key]['class'];
      $class = new $className;
      $matter[$ci_key] = $class::load($ci_value);
    }
    $matterClass = Core::getMatterClassFromType($this->type);
    return new $matterClass($matter);
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
        'default_value' => Person::getCurrentUser(),
        'class' => '\\Nick\\User\\User',
      ],
      'status' => [
        'type' => 'tinyint',
        'length' => 1,
        'default_value' => self::UNPUBLISHED,
        'form' => [
          'type' => 'select',
          'name' => 'status',
          'label' => 'Status',
          'options' => [
            self::PUBLISHED => 'Published',
            self::UNPUBLISHED => 'Unpublished',
          ],
        ],
      ],
    ];
  }

  /**
   * @return bool|array
   */
  public function getAllFields() {
    if (!$this->getType()) {
      return FALSE;
    }

    $fields_storage = \Nick::Database()
      ->select('matter_storage')
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
   * @param string $type
   * @param array $values
   *
   * @return MatterInterface|bool
   */
  public function getStorage($type, $values = []) {
    $matterClass = Core::getMatterClassFromType($type);
    if (!$matterClass) {
      return FALSE;
    }
    $matterClass = new $matterClass($values);
    return $matterClass;
  }

  /**
   * Sets matter type
   *
   * @param $type
   *
   * @return Matter
   */
  protected function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @return array|NULL
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * {@inheritDoc}
   */
  public function getValue(string $key) {
    return $this->values[$key] ?? NULL;
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
   * Massages value array to comply with fields order
   *
   * @return array
   */
  protected function massageValueArray() {
    $values = $this->values;
    $fields = static::fields();
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
   * @return array
   */
  protected function getUniqueFields() {
    $fields = static::fields();
    $unique_fields = [];
    foreach ($fields as $field => $options) {
      if (isset($options['unique']) && $options['unique']) {
        $unique_fields[] = $field;
      }
    }
    return $unique_fields;
  }

  /**
   * @param string $type
   *
   * @return bool
   */
  protected function addMatter($type) {
    $query = $this->database->insert('matter')
      ->values([
        'id' => 0,
        'type' => $type,
      ]);
    return $query->execute();
  }

  /**
   * {@inheritDoc}
   */
  public function save() {
    // Fire presave event
    $presaveEvent = new Event('MatterPresave');
    $presaveEvent->fireEvent($this);

    $table = 'matter__' . $this->type;
    // Check if item exists, update existing item or insert new item.
    if ($this->id() !== NULL) {
      $check = $this->database->select($table)
        ->condition('id', $this->id());
      if (!$result = $check->execute()) {
        \Nick::Logger()->add('[Matter][save][278]: Something went wrong trying to execute query', Logger::TYPE_FAILURE, 'Matter');
        return FALSE;
      }
      if (!$result->fetchAllAssoc()) {
        $check_existing = $this->database->select($table);
        foreach ($this->getUniqueFields() as $field) {
          $check_existing->condition($field, $this->getValue($field));
        }
        if (!$result_existing = $check_existing->execute()) {
          \Nick::Logger()->add('[Matter][save][285]: Something went wrong trying to execute query', Logger::TYPE_FAILURE, 'Matter');
          return FALSE;
        }
        if ($result = $result_existing->fetchAllAssoc()) {
          \Nick::Logger()->add('[Matter][save][299]: Some field already exists.', Logger::TYPE_FAILURE, 'Matter');
          return FALSE;
        }

        $values = ['id' => 0] + $this->massageValueArray();
        $query = $this->database->insert($table)
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
        $query = $this->database->update($table)
          ->condition('id', $this->id())
          ->values($values);
      }
    } else {
      if (!$this->addMatter($this->getType())) {
        \Nick::Logger()->add('[Matter][save][308]: Something went wrong trying to execute query', Logger::TYPE_FAILURE, 'Matter');
        return FALSE;
      }
      $id = $this->database->select('INFORMATION_SCHEMA.TABLES')
        ->fields(NULL, ['AUTO_INCREMENT'])
        ->condition('TABLE_SCHEMA', $this->database->getDatabaseName())
        ->condition('TABLE_NAME', 'matter');
      if (!$id_result = $id->execute()) {
        \Nick::Logger()->add('[Matter][save][312]: Something went wrong trying to execute query', Logger::TYPE_FAILURE, 'Matter');
        return FALSE;
      }
      $result = $id_result->fetchAllAssoc();
      // Autoincrement ID (next ID) => current ID
      $id = reset($result)['AUTO_INCREMENT'] - 1;
      $values = ['id' => $id] + $this->massageValueArray();
      $query = $this->database->insert($table)
        ->values($values);
    }

    if (!$query->execute()) {
      return FALSE;
    }

    // Fire postsave event
    $presaveEvent = new Event('MatterPostsave');
    $presaveEvent->fireEvent($this);
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
  public function owner() {
    return Person::load($this->getValue('owner'));
  }

  /**
   * @param $type
   *
   * @return bool
   */
  protected static function createMatter($type) {
    if (Core::matterInstalled($type)) {
      return FALSE;
    }
    $database = \Nick::Database();
    $results = [];
    $auto_increment = FALSE;
    $fields_storage = Matter::fields() + static::initialFields();
    $fields = '';
    foreach ($fields_storage as $field => $options) {
      if (!in_array(strtoupper($options['type']), Database::getFieldTypes())) {
        \Nick::Logger()->add('[Matter][createMatter]: Field type /' . $options['type'] . '/ does not comply to possible field types.', Logger::TYPE_WARNING, 'Matter');
      }
      if ($fields !== '') {
        $fields .= ',' . PHP_EOL;
      }
      $fields .= '  ' . Database::createFieldQuery($field, $options);

      if (isset($options['auto_increment']) && $options['auto_increment'] !== FALSE) {
        $auto_increment = $field;
      }
    }

    $fullClassName = explode('\\', static::class);
    $className = array_pop($fullClassName);
    $database->insert('matter_storage')
      ->values([
        'type' => $type,
        'label' => $className,
        'description' => 'This item creates the ' . $className . ' matter.',
        'fields' => serialize(static::initialFields()),
      ])
      ->execute();
    $query = $database->query('CREATE TABLE `matter__' . $type . '` (
' . $fields . '
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    if (!$results[] = $query) {
      \Nick::Logger()->add('[Matter][createMatter][385]: Something went wrong while querying the create', Logger::TYPE_WARNING, 'Matter');
    }

    if ($auto_increment !== FALSE) {
      $add_primary_key = $database->query('ALTER TABLE `matter__' . $type . '`
ADD PRIMARY KEY (`' . $auto_increment . '`);');
      $results[] = $add_primary_key;
      $add_auto_increment = $database->query('ALTER TABLE `matter__' . $type . '`
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
}