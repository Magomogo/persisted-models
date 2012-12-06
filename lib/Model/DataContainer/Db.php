<?php
namespace Model\DataContainer;
use Model\DataType\DataTypeInterface;
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

        /** @var DataTypeInterface $property */
        foreach ($propertyBag as $name => $property) {
            $property->setValue($row[$name]);
        }

        return $propertyBag;
    }

    public function saveProperties(PropertyBag $propertyBag)
    {
        $row = array();
        /** @var DataTypeInterface $property */
        foreach ($propertyBag as $name => $property) {
            $row[$name] = $this->dbValue($property);
        }
        $id = $this->commit($row, $propertyBag->id);
        $propertyBag->persisted($id);

        return $propertyBag;
    }

//----------------------------------------------------------------------------------------------------------------------

    private function dbValue(DataTypeInterface $property)
    {
        if (is_scalar($property->value())) {
            return $property->value();
        } elseif ($property->value() instanceof ContainerReadyInterface) {
            return $this->putIntoItsDataContainer($property->value());
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

    private function putIntoItsDataContainer(ContainerReadyInterface $model)
    {
        $container = new self(get_class($model), $this->db);
        return $model->putIn($container);
    }
}
