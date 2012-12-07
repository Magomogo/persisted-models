<?php
namespace Model\DataContainer;
use Doctrine\DBAL\Connection;
use Model\PropertyBag;
use Model\ContainerReadyInterface;

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

    public function loadProperties(PropertyBag $propertyBag)
    {
        $row = $this->begin($propertyBag->id, self::propertiesDbTable($propertyBag));

        foreach ($propertyBag as $name => &$property) {
            $property = $this->fromDbValue($property, array_key_exists($name, $row) ? $row[$name] : null);
        }

        return $this->collectReferences($row);
    }

    public function saveProperties(PropertyBag $propertyBag)
    {
        $row = array();
        foreach ($propertyBag as $name => $property) {
            $row[$name] = $this->toDbValue($property);
        }
        $id = $this->commit($row, $propertyBag->id, self::propertiesDbTable($propertyBag));
        $propertyBag->persisted($id);

        return $propertyBag;
    }

//----------------------------------------------------------------------------------------------------------------------

    private function fromDbValue($property, $column)
    {
        if ($property instanceof ContainerReadyInterface) {
            return $property::loadFrom($this, $column);
        }
        return $column;
    }

    private function toDbValue($property)
    {
        if (is_scalar($property)) {
            return $property;
        } elseif ($property instanceof ContainerReadyInterface) {
            return $property->putIn($this);
        } else {
            return null;
        }
    }

    private function begin($id, $table)
    {
        if (!is_null($id)) {
            return $this->db->fetchAssoc("SELECT * FROM $table WHERE id=?", array($id));
        }
        return array();
    }

    private function commit(array $row, $id, $table)
    {
        if (is_null($id)) {
            $this->db->insert($table, $row);
            $id = $this->db->lastInsertId();
        } else {
            $this->db->update($table, $row, array('id' => $id));
        }
        return $id;
    }

    private static function propertiesDbTable(PropertyBag $propertyBag)
    {
        return strtolower(str_replace('\\', '_', get_class($propertyBag)));
    }

    private function collectReferences(array $row)
    {
        $references = array();
        if (array_key_exists('company_properties', $row)) {
            $companyProperties = new \Company\Properties($row['company_properties']);
            $subReferences = $this->loadProperties($companyProperties);
            $references = array_merge(
                $references,
                $subReferences,
                array('company_properties' => $companyProperties)
            );
        }
        return $references;
    }
}
