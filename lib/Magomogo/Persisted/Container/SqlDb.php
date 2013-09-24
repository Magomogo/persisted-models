<?php
namespace Magomogo\Persisted\Container;

use Doctrine\DBAL\Connection;
use Magomogo\Persisted\Collection;
use Magomogo\Persisted\Container\SqlDb\NamesInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\PossessionInterface;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Exception;

class SqlDb implements ContainerInterface
{
    /**
     * @var string
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
        if ($properties instanceof PossessionInterface) {
            $this->collectReferences($row, $properties->foreign());
        }
        if ($properties instanceof Collection\OwnerInterface) {
            $this->loadCollections($properties->collections(), $properties);
        }

        return $properties;
    }

    /**
     * @param AbstractProperties $properties
     * @return AbstractProperties
     */
    public function saveProperties($properties)
    {
        $row = array();
        if ($properties instanceof PossessionInterface) {
            $row = $this->foreignKeys($properties->foreign());
        }
        if (!is_null($properties->id($this))) {
            $row['id'] = $properties->id($this);
        }
        foreach ($properties as $name => $property) {
            $row[$this->db->quoteIdentifier($name)] = $this->toDbValue($property);
        }
        $this->commit($row, $properties);

        if ($properties instanceof Collection\OwnerInterface) {
            $this->saveCollections($properties->collections(), $properties);
        }

        return $properties;
    }

    /**
     * @param AbstractProperties $properties
     * @return void
     */
    public function deleteProperties($properties)
    {
        $this->db->delete($this->names->propertiesToName($properties), array('id' => $properties->id($this)));
    }

    /**
     * @param Collection\AbstractCollection $collection
     * @param AbstractProperties $leftProperties
     * @param AbstractProperties[] $manyProperties
     */
    public function referToMany($collection, $leftProperties, array $manyProperties)
    {
        $referenceName = $this->names->manyToManyRelationName($collection, $leftProperties);

        $this->db->delete(
            $this->db->quoteIdentifier($referenceName),
            array(
                $this->db->quoteIdentifier($this->names->propertiesToName($leftProperties)) =>
                    $leftProperties->id($this)
            )
        );

        foreach ($manyProperties as $rightProperties) {
            $this->db->insert(
                $this->db->quoteIdentifier($referenceName),
                array(
                    $this->db->quoteIdentifier($this->names->propertiesToName($leftProperties)) =>
                        $leftProperties->id($this),
                    $this->db->quoteIdentifier($this->names->propertiesToName($rightProperties)) =>
                        $rightProperties->id($this),
                )
            );
        }
    }

    /**
     * @param Collection\AbstractCollection $collection
     * @param AbstractProperties $leftProperties
     * @return AbstractProperties[]
     */
    public function listReferences($collection, $leftProperties)
    {
        $referenceName = $this->names->manyToManyRelationName($collection, $leftProperties);
        $leftPropertiesName = $this->names->propertiesToName($leftProperties);

        $list = $this->db->fetchAll(
            'SELECT * FROM ' . $this->db->quoteIdentifier($referenceName)
            . ' WHERE ' . $this->db->quoteIdentifier($leftPropertiesName) . '=?',
            array($leftProperties->id($this))
        );

        $manyProperties = array();

        if (!empty($list)) {
            $rightPropertiesName = $this->names->collectionToName($collection);

            foreach ($list as $row) {
                $rightProperties = $collection->element();
                $manyProperties[] = $rightProperties->loadFrom($this, $row[$rightPropertiesName]);
            }
        }

        return $manyProperties;
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
                . ' WHERE id=?',
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

        if (!$properties->id($this)) {
            $this->db->insert($this->db->quoteIdentifier($tableName), $row);
            $properties->persisted($properties->naturalKey() ?: $this->db->lastInsertId($tableName . '_id_seq'), $this);
        } else {
            $this->db->update($this->db->quoteIdentifier($tableName), $row, array('id' => $properties->id($this)));
        }

        return $properties;
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
     * @param Collection\OwnerInterface $ownerProperties
     */
    private function loadCollections($collections, $ownerProperties)
    {
        foreach ($collections as $collection) {
            $collection->loadFrom($this, $ownerProperties);
        }
    }

    /**
     * @param Collection\AbstractCollection[] $collections
     * @param Collection\OwnerInterface $ownerProperties
     */
    private function saveCollections($collections, $ownerProperties)
    {
        foreach ($collections as $collection) {
            $collection->putIn($this, $ownerProperties);
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
}
