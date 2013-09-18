<?php
namespace Magomogo\Persisted\Container;

use Doctrine\DBAL\Connection;
use Magomogo\Persisted\CollectionOwnerInterface;
use Magomogo\Persisted\Container\SqlDb\NamesInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\PossessionInterface;
use Magomogo\Persisted\PropertyBagCollection;
use Magomogo\Persisted\PropertyBag;
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
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag
     */
    public function loadProperties($propertyBag)
    {
        $row = $this->begin($propertyBag);

        foreach ($propertyBag as $name => &$property) {
            $property = array_key_exists($name, $row) ? $this->fromDbValue($property, $row[$name]) : null;
        }
        if ($propertyBag instanceof PossessionInterface) {
            $this->collectReferences($row, $propertyBag->foreign());
        }
        if ($propertyBag instanceof CollectionOwnerInterface) {
            $this->loadCollections($propertyBag);
        }

        return $propertyBag;
    }

    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag
     */
    public function saveProperties($propertyBag)
    {
        $row = array();
        if ($propertyBag instanceof PossessionInterface) {
            $row = $this->foreignKeys($propertyBag->foreign());
        }
        if (!is_null($propertyBag->id($this))) {
            $row['id'] = $propertyBag->id($this);
        }
        foreach ($propertyBag as $name => $property) {
            $row[$this->db->quoteIdentifier($name)] = $this->toDbValue($property);
        }
        $this->commit($row, $propertyBag);

        if ($propertyBag instanceof CollectionOwnerInterface) {
            $this->saveCollections($propertyBag);
        }

        return $propertyBag;
    }

    /**
     * @param array $propertyBags
     */
    public function deleteProperties(array $propertyBags)
    {
        foreach ($propertyBags as $bag) {
            $this->db->delete($this->names->classToName($bag), array('id' => $bag->id($this)));
        }
    }

    /**
     * @param PropertyBagCollection $collectionBag
     * @param \Magomogo\Persisted\PropertyBag $leftProperties
     * @param $connections
     * @internal param array $connections
     */
    public function referToMany($collectionBag, $leftProperties, array $connections)
    {
        $referenceName = $this->names->manyToManyRelationName($collectionBag, $leftProperties);

        $this->db->delete(
            $this->db->quoteIdentifier($referenceName),
            array($this->db->quoteIdentifier($this->names->classToName($leftProperties)) => $leftProperties->id($this))
        );

        /** @var PropertyBag $rightProperties */
        foreach ($connections as $rightProperties) {
            $this->db->insert(
                $this->db->quoteIdentifier($referenceName),
                array(
                    $this->db->quoteIdentifier($this->names->classToName($leftProperties)) => $leftProperties->id($this),
                    $this->db->quoteIdentifier($this->names->classToName($rightProperties)) => $rightProperties->id($this),
                )
            );
        }
    }

    /**
     * @param PropertyBagCollection $collectionBag
     * @param \Magomogo\Persisted\PropertyBag $leftProperties
     * @return array
     */
    public function listReferences($collectionBag, $leftProperties)
    {
        $referenceName = $this->names->manyToManyRelationName($collectionBag, $leftProperties);
        $leftPropertiesName = $this->names->classToName($leftProperties);

        $list = $this->db->fetchAll(
            'SELECT * FROM ' . $this->db->quoteIdentifier($referenceName)
            . ' WHERE ' . $this->db->quoteIdentifier($leftPropertiesName) . '=?',
            array($leftProperties->id($this))
        );

        $connections = array();

        if (!empty($list)) {
            $rightPropertiesName = self::rightPropertiesName($list[0], $leftPropertiesName);

            foreach ($list as $row) {
                $props = $this->names->nameToClass($rightPropertiesName);
                $connections[] = $props->loadFrom($this, $row[$rightPropertiesName]);
            }
        }

        return $connections;
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
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return array
     * @throws \Magomogo\Persisted\Exception\NotFound
     */
    private function begin($propertyBag)
    {
        if (!is_null($propertyBag->id($this))) {

            $row = $this->db->fetchAssoc(
                'SELECT * FROM ' . $this->db->quoteIdentifier($this->names->classToName($propertyBag)) . ' WHERE id=?',
                array($propertyBag->id($this))
            );

            if (is_array($row)) {
                return $row;
            }
        }

        throw new Exception\NotFound;
    }

    /**
     * @param array $row
     * @param \Magomogo\Persisted\PropertyBag $properties
     * @return \Magomogo\Persisted\PropertyBag
     */
    private function commit(array $row, $properties)
    {
        $tableName = $this->names->classToName($properties);

        if (!$properties->id($this)) {
            $this->db->insert($this->db->quoteIdentifier($tableName), $row);
            $properties->persisted($properties->naturalKey() ?: $this->db->lastInsertId($tableName . '_id_seq'), $this);
        } else {
            $this->db->update($this->db->quoteIdentifier($tableName), $row, array('id' => $properties->id($this)));
        }

        return $properties;
    }

    private function collectReferences(array $row, $references)
    {
        /* @var PropertyBag $properties */
        foreach ($references as $referenceName => $properties) {
            $properties->loadFrom($this, $row[$referenceName]);
        }
        return $references;
    }

    /**
     * @param CollectionOwnerInterface $propertyBag
     */
    private function loadCollections($propertyBag)
    {
        /** @var PropertyBagCollection $collection */
        foreach ($propertyBag->collections() as $collection) {
            $collection->loadFrom($this, $propertyBag);
        }
    }

    /**
     * @param CollectionOwnerInterface $propertyBag
     */
    private function saveCollections($propertyBag)
    {
        /** @var PropertyBagCollection $collection */
        foreach ($propertyBag->collections() as $collection) {
            $collection->putIn($this, $propertyBag);
        }
    }

    private function foreignKeys($references)
    {
        $keys = array();
        /* @var PropertyBag $properties */
        foreach ($references as $referenceName => $properties) {
            $keys[$this->db->quoteIdentifier($referenceName)] = $properties->id($this);
        }

        return $keys;
    }

    private function rightPropertiesName($row, $leftPropertiesName)
    {
        unset($row[$leftPropertiesName]);
        reset($row);
        return key($row);
    }
}
