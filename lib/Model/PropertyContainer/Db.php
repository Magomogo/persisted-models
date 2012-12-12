<?php
namespace Model\PropertyContainer;
use Doctrine\DBAL\Connection;
use Model\PropertyBag;
use Model\ContainerReadyInterface;
use Model\Exception;

class Db implements ContainerInterface
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @param Connection $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function loadProperties(PropertyBag $propertyBag, array $references = array())
    {
        $row = $this->begin($propertyBag->id, self::classToName($propertyBag));

        foreach ($propertyBag as $name => &$property) {
            $property = $this->fromDbValue($property, array_key_exists($name, $row) ? $row[$name] : null);
            unset($row[$name]);
        }
        $propertyBag->persisted($propertyBag->id, $this);
        $this->collectReferences($row, $references);
        return $propertyBag;
    }

    public function saveProperties(PropertyBag $propertyBag, array $references = array())
    {
        $row = $this->foreignKeys($references);
        foreach ($propertyBag as $name => $property) {
            $row[$name] = $this->toDbValue($property);
        }
        $id = $this->commit($row, $propertyBag);

        return $propertyBag;
    }

    public function referToMany($referenceName, PropertyBag $leftProperties, array $connections)
    {
        $this->db->delete($referenceName, array(self::classToName($leftProperties) => $leftProperties->id));

        /** @var PropertyBag $propertyBag */
        foreach ($connections as $rightProperties) {
            $this->db->insert($referenceName, array(
                self::classToName($leftProperties) => $leftProperties->id,
                self::classToName($rightProperties) => $rightProperties->id,
            ));
        }
    }

    public function listReferences($referenceName, PropertyBag $leftProperties, PropertyBag $rightPropertiesType)
    {
        $rightPropertiesName = self::classToName($rightPropertiesType);

        $statement = $this->db->executeQuery(
            "SELECT $rightPropertiesName FROM $referenceName WHERE " . self::classToName($leftProperties) . '=?',
            array($leftProperties->id)
        );

        $connections = array();
        while ($id = $statement->fetchColumn()) {
            $className = get_class($rightPropertiesType);
            $properties = new $className($id);
            $this->loadProperties($properties);
            $connections[] = $properties;
        }

        return $connections;
    }

//----------------------------------------------------------------------------------------------------------------------

    private function fromDbValue($property, $column)
    {
        if ($property instanceof ContainerReadyInterface) {
            return $property::loadFrom($this, $column);
        } elseif($property instanceof \DateTime) {
            return new \DateTime($column);
        }
        return $column;
    }

    private function toDbValue($property)
    {
        if (is_scalar($property)) {
            return $property;
        } elseif ($property instanceof ContainerReadyInterface) {
            return $property->putIn($this);
        } elseif ($property instanceof \DateTime) {
            return $property->format('c');
        } else {
            throw new Exception\Type;
        }
    }

    private function begin($id, $table)
    {
        if (!is_null($id)) {
            return $this->db->fetchAssoc("SELECT * FROM $table WHERE id=?", array($id));
        }
        return array();
    }

    private function commit(array $row, PropertyBag $properties)
    {
        if (is_null($properties->id)) {
            $this->db->insert(self::classToName($properties), $row);
            $properties->persisted($this->db->lastInsertId(), $this);
        } else {
            $this->db->update(self::classToName($properties), $row, array('id' => $properties->id));
        }
        return $properties->id;
    }

    private static function classToName($object)
    {
        return strtolower(str_replace('\\', '_', get_class($object)));
    }

    private function collectReferences(array $row, array $references)
    {
        /* @var PropertyBag $properties */
        foreach ($references as $referenceName => $properties) {
            $properties->persisted($row[$referenceName], $this);
            $this->loadProperties($properties);
        }
        return $references;
    }

    private function foreignKeys(array $references = array())
    {
        $keys = array();
        /* @var PropertyBag $properties */
        foreach ($references as $referenceName => $properties) {
            $keys[$referenceName] = $properties->id;
        }

        return $keys;
    }
}
