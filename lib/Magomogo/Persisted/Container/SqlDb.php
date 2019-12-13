<?php
namespace Magomogo\Persisted\Container;

use Doctrine\DBAL\Connection;
use Magomogo\Persisted\Collection;
use Magomogo\Persisted\Container\SqlDb\NamesInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Exception;

class SqlDb implements ContainerInterface
{
    /**
     * @var NamesInterface
     */
    private $names;

    /**
     * @var Connection
     */
    private $db;

    /**
     * @param Connection $db
     * @param NamesInterface $names
     */
    public function __construct($db, $names)
    {
        $this->db = $db;
        $this->names = $names;
    }

    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties
     */
    public function loadProperties($properties)
    {
        $row = $this->begin($properties);

        foreach ($properties as $name => &$property) {
            $property = array_key_exists($name, $row) ? $this->fromDbValue($property, $row[$name]) : null;
        }
        $this->collectReferences($row, $properties->foreign());
        $this->loadCollections($properties->collections(), $properties);

        return $properties;
    }

    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties
     */
    public function saveProperties($properties)
    {
        $row = $this->foreignKeys($properties->foreign());

        if (!is_null($properties->id($this))) {
            $row['id'] = $properties->id($this);
        }
        foreach ($properties as $name => $property) {
            $row[$this->db->quoteIdentifier($name)] = $this->toDbValue($property);
        }

        $this->commit($row, $properties);
        $this->saveCollections($properties->collections(), $properties);

        return $properties;
    }

    /**
     * @param AbstractProperties $properties
     * @return void
     */
    public function deleteProperties($properties)
    {
        $this->db->delete(
            $this->db->quoteIdentifier($this->names->propertiesToName($properties)),
            array('id' => $properties->id($this))
        );
    }

//----------------------------------------------------------------------------------------------------------------------

    private function fromDbValue($property, $value)
    {
        if ($property instanceof ModelInterface) {
            return is_null($value) ? null : $property::load($this, $value);
        } elseif($property instanceof \DateTime) {
            return self::dateInIso8601($value);
        }
        return $value;
    }

    private static function dateInIso8601($str)
    {
        return new \DateTime(date('c', strtotime($str)));
    }

    private function toDbValue($property)
    {
        if (is_scalar($property) || is_null($property)) {
            return $property;
        } elseif ($property instanceof ModelInterface) {
            return $property->save($this);
        } elseif ($property instanceof \DateTime) {
            return $property->format('c');
        } else {
            throw new Exception\Type;
        }
    }

    /**
     * @param AbstractProperties $properties
     * @return array
     * @throws Exception\NotFound
     */
    private function begin($properties)
    {
        if (!is_null($properties->id($this))) {

            $row = $this->db->fetchAssoc(
                'SELECT * FROM ' . $this->db->quoteIdentifier($this->names->propertiesToName($properties))
                . ' WHERE ' . $this->db->quoteIdentifier(($properties->naturalKeyFieldName() ?: 'id')) . '=?',
                array($properties->id($this))
            );

            if (is_array($row)) {
                return $row;
            }
        }

        throw new Exception\NotFound;
    }

    /**
     * @param array $row
     * @param AbstractProperties $properties
     * @return AbstractProperties
     */
    private function commit(array $row, $properties)
    {
        $tableName = $this->names->propertiesToName($properties);
        $types = $this->propertiesPDOTypes($properties);
        if (!$properties->id($this)) {
            $this->db->insert($this->db->quoteIdentifier($tableName), $row, $types);
            $properties->persisted($this->defineNewId($properties, $tableName), $this);
        } else {
            $this->db->update($this->db->quoteIdentifier($tableName), $row, array('id' => $properties->id($this)),
                $types);
        }

        return $properties;
    }

    /**
     * @param AbstractProperties $properties
     * @return array|null
     */
    private function propertiesPDOTypes($properties)
    {
        $types = array();
        foreach ($properties as $name => $property) {
            if (is_bool($property)) {
                $types[$name] = \PDO::PARAM_BOOL;
            }
        }
        return $types;
    }

    /**
     * @param array $row
     * @param AbstractProperties[] $references
     * @return AbstractProperties[]
     */
    private function collectReferences(array $row, $references)
    {
        foreach ($references as $referenceName => $properties) {
            $properties->loadFrom($this, $row[$referenceName]);
        }
        return $references;
    }

    /**
     * @param Collection\AbstractCollection[] $collections
     * @param AbstractProperties $ownerProperties
     */
    private function loadCollections($collections, $ownerProperties)
    {
        $leftPropertiesName = $this->names->propertiesToName($ownerProperties);

        foreach ($collections as $collection) {

            $referenceName = $this->names->manyToManyRelationName($collection, $ownerProperties);
            $rightPropertiesName = $this->names->collectionToName($collection);

            $list = $this->db->fetchAll(
                'SELECT * FROM ' . $this->db->quoteIdentifier($referenceName)
                . ' WHERE ' . $this->db->quoteIdentifier($leftPropertiesName) . '=?',
                array($ownerProperties->id($this))
            );

            $container = $this;
            $collection->propertiesOperation(
                function() use ($rightPropertiesName, $collection, $list, $container) {
                    $items = array();

                    if (!empty($list)) {
                        foreach ($list as $row) {
                            $rightProperties = $collection->constructProperties();
                            $items[] = $rightProperties->loadFrom($container, $row[$rightPropertiesName]);
                        }
                    }

                    return $items;
                }
            );
        }
    }

    /**
     * @param Collection\AbstractCollection[] $collections
     * @param AbstractProperties $ownerProperties
     */
    private function saveCollections($collections, $ownerProperties)
    {

        foreach ($collections as $collection) {
            $referenceName = $this->names->manyToManyRelationName($collection, $ownerProperties);

            $this->db->delete(
                $this->db->quoteIdentifier($referenceName),
                array(
                    $this->db->quoteIdentifier($this->names->propertiesToName($ownerProperties)) =>
                    $ownerProperties->id($this)
                )
            );

            $dbAdapter = $this->db;
            $container = $this;
            $names = $this->names;

            $collection->propertiesOperation(
                function($items) use ($referenceName, $dbAdapter, $ownerProperties, $container, $names) {
                    foreach ($items as $rightProperties) {
                        $dbAdapter->insert(
                            $dbAdapter->quoteIdentifier($referenceName),
                            array(
                                $dbAdapter->quoteIdentifier($names->propertiesToName($ownerProperties)) =>
                                $ownerProperties->id($container),
                                $dbAdapter->quoteIdentifier($names->propertiesToName($rightProperties)) =>
                                $rightProperties->id($container),
                            )
                        );
                    }
                    return $items;
                }
            );
        }
    }

    /**
     * @param AbstractProperties[] $references
     * @return array
     */
    private function foreignKeys($references)
    {
        $keys = array();
        foreach ($references as $referenceName => $properties) {
            $keys[$this->db->quoteIdentifier($referenceName)] = $properties->id($this);
        }

        return $keys;
    }

    /**
     * @param AbstractProperties $properties
     * @param string $tableName
     * @return string
     */
    private function defineNewId($properties, $tableName)
    {
        return $properties->naturalKeyFieldName() ?
            $properties->{$properties->naturalKeyFieldName()} : $this->db->lastInsertId($tableName . '_id_seq');
    }
}
