<?php
namespace Magomogo\Model\PropertyContainer;
use Doctrine\DBAL\Connection;
use Magomogo\Model\PropertyBag;
use Magomogo\Model\ContainerReadyInterface;
use Magomogo\Model\Exception;

class Db implements ContainerInterface
{
    /**
     * @var string
     */
    private $modelsNamespace;

    /**
     * @var Connection
     */
    private $db;

    /**
     * @param Connection $db
     * @param string $modelsNamespace
     */
    public function __construct($db, $modelsNamespace = '')
    {
        $this->db = $db;
        $this->modelsNamespace = $modelsNamespace;
    }

    public function loadProperties(PropertyBag $propertyBag, array $references = array())
    {
        $row = $this->begin($propertyBag);
        $propertyBag->assertOriginIs($this);

        foreach ($propertyBag as $name => &$property) {
            $property = $this->fromDbValue($property, $row[$name]);
        }
        $this->collectReferences($row, $references);

        return $propertyBag;
    }

    public function saveProperties(PropertyBag $propertyBag, array $references = array())
    {
        $row = $this->foreignKeys($references);
        if (!is_null($propertyBag->id)) {
            $row['id'] = $propertyBag->id;
        }
        foreach ($propertyBag as $name => $property) {
            $row[$name] = $this->toDbValue($property);
        }
        return $this->commit($row, $propertyBag);
    }

    public function deleteProperties(array $propertyBags)
    {
        foreach ($propertyBags as $bag) {
            $this->db->delete($this->classToName($bag), array('id' => $bag->id));
        }
    }

    public function referToMany($referenceName, PropertyBag $leftProperties, array $connections)
    {
        $this->db->delete($referenceName, array($this->classToName($leftProperties) => $leftProperties->id));

        /** @var PropertyBag $propertyBag */
        foreach ($connections as $rightProperties) {
            $this->db->insert($referenceName, array(
                $this->classToName($leftProperties) => $leftProperties->id,
                $this->classToName($rightProperties) => $rightProperties->id,
            ));
        }
    }

    public function listReferences($referenceName, PropertyBag $leftProperties, $rightPropertiesClassName)
    {
        $rightPropertiesName = $this->classToName($rightPropertiesClassName);

        $statement = $this->db->executeQuery(
            "SELECT $rightPropertiesName FROM $referenceName WHERE " . $this->classToName($leftProperties) . '=?',
            array($leftProperties->id)
        );

        $connections = array();
        while ($id = $statement->fetchColumn()) {
            $properties = new $rightPropertiesClassName($id);
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
        if (is_scalar($property) || is_null($property)) {
            return $property;
        } elseif ($property instanceof ContainerReadyInterface) {
            return $property->putIn($this);
        } elseif ($property instanceof \DateTime) {
            return $property->format('c');
        } else {
            throw new Exception\Type;
        }
    }

    private function begin(PropertyBag $propertyBag)
    {
        if (!is_null($propertyBag->id)) {
            $table = $this->classToName($propertyBag);
            $row = $this->db->fetchAssoc("SELECT * FROM $table WHERE id=?", array($propertyBag->id));

            if (is_array($row)) {
                $propertyBag->persisted($propertyBag->id, $this);
                return $row;
            } else {
                throw new Exception\NotFound;
            }
        }
        return array();
    }

    private function commit(array $row, PropertyBag $properties)
    {
        $this->confirmPersistency($properties);

        if (!$properties->isPersistedIn($this)) {
            $this->db->insert($this->classToName($properties), $row);
            $properties->persisted($properties->id ?: $this->db->lastInsertId(), $this);
        } else {
            $this->db->update($this->classToName($properties), $row, array('id' => $properties->id));
        }

        return $properties;
    }

    private function confirmPersistency(PropertyBag $properties)
    {
        try {
            $properties->assertOriginIs($this);
        } catch (Exception\Origin $e) {
            if ($properties->id && $this->db->fetchColumn(
                'SELECT 1 FROM ' . $this->classToName($properties) . ' WHERE id=?', array($properties->id)
            )) {
                $properties->persisted($properties->id, $this);
            }
        }
    }

    private function classToName($class)
    {
        $name = strtolower(str_replace('\\', '_', is_object($class) ? get_class($class) : $class));
        $namespacePart = strtolower(str_replace('\\', '_', $this->modelsNamespace));
        return preg_replace('/^' . preg_quote($namespacePart) . '/', '', $name);
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
