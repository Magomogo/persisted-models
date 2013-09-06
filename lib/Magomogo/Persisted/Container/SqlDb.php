<?php
namespace Magomogo\Persisted\Container;

use Doctrine\DBAL\Connection;
use Magomogo\Persisted\Container\SqlDb\NamesInterface;
use Magomogo\Persisted\ModelInterface;
use Magomogo\Persisted\PossessionInterface;
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

        return $this->commit($row, $propertyBag);
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
     * @param string $referenceName
     * @param \Magomogo\Persisted\PropertyBag $leftProperties
     * @param array $connections
     */
    public function referToMany($referenceName, $leftProperties, array $connections)
    {
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
     * @param string $referenceName
     * @param \Magomogo\Persisted\PropertyBag $leftProperties
     * @return array
     */
    public function listReferences($referenceName, $leftProperties)
    {
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

    private function fromDbValue($property, $column)
    {
        if ($property instanceof ModelInterface) {
            return is_null($column) ? null : $property::load($this, $column);
        } elseif($property instanceof \DateTime) {
            return new \DateTime($column);
        }
        return $column;
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
        $this->confirmPersistency($properties);

        $tableName = $this->names->classToName($properties);

        if (!$properties->id($this)) {
            $this->db->insert($this->db->quoteIdentifier($tableName), $row);
            $properties->persisted($properties->naturalKey() ?: $this->db->lastInsertId($tableName . '_id_seq'), $this);
        } else {
            $this->db->update($this->db->quoteIdentifier($tableName), $row, array('id' => $properties->id($this)));
        }

        return $properties;
    }

    /**
     * @param \Magomogo\Persisted\PropertyBag $properties
     */
    private function confirmPersistency($properties)
    {
        if (
            $properties->id($this)
            &&
            $this->db->fetchColumn(
                'SELECT 1 FROM ' . $this->db->quoteIdentifier($this->names->classToName($properties)) . ' WHERE id=?',
                array($properties->id($this))
            )
        ) {
            $properties->persisted($properties->id($this), $this);
        }
    }

    private function collectReferences(array $row, $references)
    {
        /* @var PropertyBag $properties */
        foreach ($references as $referenceName => $properties) {
            $properties->loadFrom($this, $row[$referenceName]);
        }
        return $references;
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
