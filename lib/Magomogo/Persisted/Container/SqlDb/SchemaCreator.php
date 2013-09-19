<?php

namespace Magomogo\Persisted\Container\SqlDb;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\PossessionInterface;
use Magomogo\Persisted\Collection;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Exception;

class SchemaCreator implements ContainerInterface
{
    /**
     * @var AbstractSchemaManager
     */
    private $manager;

    /**
     * @var NamesInterface
     */
    private $names;

    /**
     * @param $manager AbstractSchemaManager
     * @param $names NamesInterface
     */
    public function __construct($manager, $names)
    {
        $this->manager = $manager;
        $this->names = $names;
    }

    public function schemaFor(ModelInterface $model)
    {
        $model->save($this);
    }

    public function loadProperties($properties)
    {
        trigger_error('Incorrect usage', E_USER_ERROR);
    }

    /**
     * @param \Magomogo\Persisted\AbstractProperties $properties
     * @return \Magomogo\Persisted\AbstractProperties
     */
    public function saveProperties($properties)
    {
        $tableName = $this->names->propertiesToName($properties);

        if (!in_array($tableName, $this->manager->listTableNames())) {
            $this->manager->createTable(
                $this->newTableObject($properties, $tableName)
            );

            if ($properties instanceof Collection\OwnerInterface) {
                /** @var Collection\AbstractCollection $collection */
                foreach ($properties->collections() as $collectionName => $collection) {
                    $collection->putIn($this, $properties);
                }
            }

        }

        $properties->persisted($tableName, $this);
        return $properties;
    }

    /**
     * @param array $properties array of \Magomogo\Model\AbstractProperties
     * @return void
     */
    public function deleteProperties(array $properties)
    {
        trigger_error('Incorrect usage', E_USER_ERROR);
    }

    /**
     * @param Collection\AbstractCollection $collection
     * @param \Magomogo\Persisted\AbstractProperties $leftProperties
     * @param array $manyProperties array of \Magomogo\Model\AbstractProperties
     * @return void
     */
    public function referToMany($collection, $leftProperties, array $manyProperties)
    {
        $referenceName = $this->names->manyToManyRelationName($collection, $leftProperties);

        if (!empty($manyProperties) && !in_array($referenceName, $this->manager->listTableNames())) {
            $rightProperties = reset($manyProperties);
            $table = new Table($this->quoteIdentifier($referenceName));
            $this->addForeignReferenceColumn(
                $table, $this->names->propertiesToName($leftProperties), $leftProperties
            );
            $this->addForeignReferenceColumn(
                $table, $this->names->propertiesToName($rightProperties), $rightProperties
            );
            $this->manager->createTable($table);
        }
    }

    /**
     * @param string $collection
     * @param \Magomogo\Persisted\AbstractProperties $leftProperties
     * @return array of \Magomogo\Model\AbstractProperties
     */
    public function listReferences($collection, $leftProperties)
    {
        trigger_error('Incorrect usage', E_USER_ERROR);
    }

//----------------------------------------------------------------------------------------------------------------------

    private function quoteIdentifier($str)
    {
        return $this->manager->getDatabasePlatform()->quoteIdentifier($str);
    }

    /**
     * @param Table $table
     * @param string $fieldName
     * @param mixed $fieldValue
     * @return void
     * @throws \Magomogo\Persisted\Exception\Type
     */
    private function defineSchemaForField($table, $fieldName, $fieldValue) {
        if (is_string($fieldValue)) {
            $table->addColumn($this->quoteIdentifier($fieldName), 'text', array('notNull' => false));
        } elseif ($fieldValue instanceof ModelInterface) {
            $table->addColumn($this->quoteIdentifier($fieldName), 'integer', array('unsigned' => true, 'notNull' => false));
            $relatedTable = $fieldValue->save($this);
            $table->addForeignKeyConstraint(
                $this->quoteIdentifier($relatedTable),
                array($this->quoteIdentifier($fieldName)),
                array('id'),
                array('onUpdate' => 'RESTRICT', 'onDelete' => 'SET NULL')
            );
        } elseif ($fieldValue instanceof \DateTime) {
            $table->addColumn($this->quoteIdentifier($fieldName), 'datetimetz', array('notNull' => false));
        } else {
            throw new Exception\Type;
        }
    }

    /**
     * @param AbstractProperties $properties
     * @param string $tableName
     * @return \Doctrine\DBAL\Schema\Table
     */
    private function newTableObject($properties, $tableName)
    {
        $table = new Table($this->quoteIdentifier($tableName));

        if (!isset($properties->id)) {
            $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        }

        foreach ($properties as $name => $value) {
            if (($name === 'id') && is_string($value)) {
                $table->addColumn('id', 'string', array('length' => 255, 'notnull' => true));
            } else {
                $this->defineSchemaForField($table, $name, $value);
            }
        }

        $table->setPrimaryKey(array('id'));

        if ($properties instanceof PossessionInterface) {
            foreach ($properties->foreign() as $propertyName => $foreignProperties) {
                $this->addForeignReferenceColumn($table, $propertyName, $foreignProperties);
            }
        }

        return $table;
    }

    /**
     * @param Table $table
     * @param string $columnName
     * @param AbstractProperties $leftProperties
     */
    private function addForeignReferenceColumn($table, $columnName, $leftProperties)
    {
        if ($leftProperties->naturalKey() && is_string($leftProperties->naturalKey())) {
            $table->addColumn($this->quoteIdentifier($columnName), 'string', array('length' => 255, 'notnull' => false));
        } else {
            $table->addColumn(
                $this->quoteIdentifier($columnName),
                'integer',
                array('unsigned' => true, 'notNull' => false)
            );
        }
        $table->addForeignKeyConstraint(
            $this->quoteIdentifier($this->names->propertiesToName($leftProperties)),
            array($this->quoteIdentifier($columnName)),
            array('id'),
            array('onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE')
        );
    }

}