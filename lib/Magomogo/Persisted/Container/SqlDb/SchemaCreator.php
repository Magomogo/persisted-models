<?php

namespace Magomogo\Persisted\Container\SqlDb;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Magomogo\Persisted\Container\ContainerInterface;
use Magomogo\Persisted\ModelInterface;
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

            /** @var Collection\AbstractCollection $collection */
            foreach ($properties->collections() as $collection) {

                $creator = $this;
                $collection->propertiesOperation(
                    function($items) use ($properties, $collection, $creator) {
                        $creator->many2manySchema($collection, $properties, $items);
                        return $items;
                    }
                );
            }

        }

        $properties->persisted($properties, $this);
        return $properties;
    }

    /**
     * @param \Magomogo\Persisted\AbstractProperties $properties
     * @return void
     */
    public function deleteProperties($properties)
    {
        trigger_error('Incorrect usage', E_USER_ERROR);
    }

    /**
     * @param Collection\AbstractCollection $collection
     * @param \Magomogo\Persisted\AbstractProperties $leftProperties
     * @param \Magomogo\Persisted\AbstractProperties[] $manyProperties
     * @return void
     */
    public function many2manySchema($collection, $leftProperties, array $manyProperties)
    {
        $referenceName = $this->names->manyToManyRelationName($collection, $leftProperties);

        if (!empty($manyProperties) && !in_array($referenceName, $this->manager->listTableNames())) {
            $rightProperties = reset($manyProperties);
            $table = new Table($this->quoteIdentifier($referenceName));
            $this->addForeignReferenceColumn(
                $table, $this->names->propertiesToName($leftProperties), $leftProperties,
                array('onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE')
            );
            $this->addForeignReferenceColumn(
                $table, $this->names->propertiesToName($rightProperties), $rightProperties,
                array('onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE')
            );
            $this->manager->createTable($table);
        }
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
            $this->addForeignReferenceColumn(
                $table,
                $fieldName,
                $fieldValue->save($this),
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

        if (is_null($properties->naturalKeyFieldName())) {
            $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        }

        foreach ($properties as $name => $value) {
            if ($properties->naturalKeyFieldName() === $name) {
                $table->addColumn($name, 'string', array('length' => 255, 'notnull' => true));
            } else {
                $this->defineSchemaForField($table, $name, $value);
            }
        }

        $table->setPrimaryKey(array($properties->naturalKeyFieldName() ?: 'id'));

        foreach ($properties->foreign() as $propertyName => $foreignProperties) {
            $this->addForeignReferenceColumn($table, $propertyName, $foreignProperties,
                array('onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'));
        }

        return $table;
    }

    /**
     * @param Table $table
     * @param string $columnName
     * @param AbstractProperties $leftProperties
     */
    private function addForeignReferenceColumn($table, $columnName, $leftProperties, $options)
    {
        if ($leftProperties->naturalKeyFieldName()) {
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
            array($leftProperties->naturalKeyFieldName() ?: 'id'),
            $options
        );
    }

}