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
     * @param \Magomogo\Persisted\AbstractProperties $propertyBag
     * @return \Magomogo\Persisted\AbstractProperties
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
        if ($propertyBag instanceof Collection\OwnerInterface) {
            $this->loadCollections($propertyBag);
        }

        return $propertyBag;
    }

    /**
     * @param \Magomogo\Persisted\AbstractProperties $propertyBag
     * @return \Magomogo\Persisted\AbstractProperties
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

        if ($propertyBag instanceof Collection\OwnerInterface) {
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
            $this->db->delete($this->names->propertyBagToName($bag), array('id' => $bag->id($this)));
        }
    }

    /**
     * @param Collection\AbstractCollection $collectionBag
     * @param \Magomogo\Persisted\AbstractProperties $leftProperties
     * @param array $propertyBags
     * @internal param array $connections
     */
    public function referToMany($collectionBag, $leftProperties, array $propertyBags)
    {
        $referenceName = $this->names->manyToManyRelationName($collectionBag, $leftProperties);

        $this->db->delete(
            $this->db->quoteIdentifier($referenceName),
            array(
                $this->db->quoteIdentifier($this->names->propertyBagToName($leftProperties)) =>
                    $leftProperties->id($this)
            )
        );

        /** @var AbstractProperties $rightProperties */
        foreach ($propertyBags as $rightProperties) {
            $this->db->insert(
                $this->db->quoteIdentifier($referenceName),
                array(
                    $this->db->quoteIdentifier($this->names->propertyBagToName($leftProperties)) =>
                        $leftProperties->id($this),
                    $this->db->quoteIdentifier($this->names->propertyBagToName($rightProperties)) =>
                        $rightProperties->id($this),
                )
            );
        }
    }

    /**
     * @param Collection\AbstractCollection $collectionBag
     * @param \Magomogo\Persisted\AbstractProperties $leftProperties
     * @return array
     */
    public function listReferences($collectionBag, $leftProperties)
    {
        $referenceName = $this->names->manyToManyRelationName($collectionBag, $leftProperties);
        $leftPropertiesName = $this->names->propertyBagToName($leftProperties);

        $list = $this->db->fetchAll(
            'SELECT * FROM ' . $this->db->quoteIdentifier($referenceName)
            . ' WHERE ' . $this->db->quoteIdentifier($leftPropertiesName) . '=?',
            array($leftProperties->id($this))
        );

        $propertyBags = array();

        if (!empty($list)) {
            $rightPropertiesName = $this->names->propertyBagCollectionToName($collectionBag);

            foreach ($list as $row) {
                $rightProperties = $this->names->nameToPropertyBag($rightPropertiesName);
                $propertyBags[] = $rightProperties->loadFrom($this, $row[$rightPropertiesName]);
            }
        }

        return $propertyBags;
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
     * @param \Magomogo\Persisted\AbstractProperties $propertyBag
     * @return array
     * @throws \Magomogo\Persisted\Exception\NotFound
     */
    private function begin($propertyBag)
    {
        if (!is_null($propertyBag->id($this))) {

            $row = $this->db->fetchAssoc(
                'SELECT * FROM ' . $this->db->quoteIdentifier($this->names->propertyBagToName($propertyBag))
                . ' WHERE id=?',
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
     * @param \Magomogo\Persisted\AbstractProperties $properties
     * @return \Magomogo\Persisted\AbstractProperties
     */
    private function commit(array $row, $properties)
    {
        $tableName = $this->names->propertyBagToName($properties);

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
        /* @var AbstractProperties $properties */
        foreach ($references as $referenceName => $properties) {
            $properties->loadFrom($this, $row[$referenceName]);
        }
        return $references;
    }

    /**
     * @param Collection\OwnerInterface $propertyBag
     */
    private function loadCollections($propertyBag)
    {
        /** @var Collection\AbstractCollection $collection */
        foreach ($propertyBag->collections() as $collection) {
            $collection->loadFrom($this, $propertyBag);
        }
    }

    /**
     * @param Collection\OwnerInterface $propertyBag
     */
    private function saveCollections($propertyBag)
    {
        /** @var Collection\AbstractCollection $collection */
        foreach ($propertyBag->collections() as $collection) {
            $collection->putIn($this, $propertyBag);
        }
    }

    private function foreignKeys($references)
    {
        $keys = array();
        /* @var AbstractProperties $properties */
        foreach ($references as $referenceName => $properties) {
            $keys[$this->db->quoteIdentifier($referenceName)] = $properties->id($this);
        }

        return $keys;
    }
}
