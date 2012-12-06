<?php
namespace Model\DataContainer;
use Doctrine\DBAL\Connection;
use Model\PropertyBag;
use Model\ContainerReadyInterface;

class Db implements ContainerInterface
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var Connection
     */
    private $db;

    /**
     * @param string $modelClassName
     * @param Connection $db
     */
    public function __construct($modelClassName, $db)
    {
        $this->table = self::convertToTableName($modelClassName);
        $this->db = $db;
    }

    public function loadProperties(PropertyBag $propertyBag)
    {
        $row = $this->begin($propertyBag->id);

        foreach ($propertyBag as $name => &$property) {
            $property = $this->fromDbValue($property, $row[$name]);
        }

        return $propertyBag;
    }

    public function saveProperties(PropertyBag $propertyBag)
    {
        $row = array();
        foreach ($propertyBag as $name => $property) {
            $row[$name] = $this->toDbValue($property);
        }
        $id = $this->commit($row, $propertyBag->id);
        $propertyBag->persisted($id);

        return $propertyBag;
    }

//----------------------------------------------------------------------------------------------------------------------

    private function fromDbValue($property, $column)
    {
        if ($property instanceof ContainerReadyInterface) {
            return $property->loadFrom($this->dataContainer($property), $column);
        }
        return $column;
    }

    private function toDbValue($property)
    {
        if (is_scalar($property)) {
            return $property;
        } elseif ($property instanceof ContainerReadyInterface) {
            return $property->putIn($this->dataContainer($property));
        } else {
            return null;
        }
    }

    private function begin($id)
    {
        if (!is_null($id)) {
            return $this->db->fetchAssoc("SELECT * FROM {$this->table} WHERE id=?", array($id));
        }
        return array();
    }

    private function commit(array $row, $id)
    {
        if (is_null($id)) {
            $this->db->insert($this->table, $row);
            $id = $this->db->lastInsertId();
        } else {
            $this->db->update($this->table, $row, array('id' => $id));
        }
        return $id;
    }

    private static function convertToTableName($modelClassName)
    {
        return strtolower(str_replace('\\', '_', $modelClassName));
    }

    private function dataContainer(ContainerReadyInterface $model)
    {
        return new self(get_class($model), $this->db);
    }
}
