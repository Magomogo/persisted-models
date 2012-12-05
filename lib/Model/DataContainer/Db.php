<?php
namespace Model\DataContainer;
use Model\DataType\DataTypeInterface;
use Doctrine\DBAL\Connection;
use Model\PropertyBag;

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
     * @var string|null
     */
    private $id;

    /**
     * @param string $modelClassName
     * @param Connection $db
     * @param $id
     */
    public function __construct($modelClassName, $db, $id = null)
    {
        $this->table = self::convertToTableName($modelClassName);
        $this->id = $id;
        $this->db = $db;
    }

    public function loadProperties(PropertyBag $propertyBag)
    {
        $row = $this->begin();

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

        return $this->commit($row);
    }

//----------------------------------------------------------------------------------------------------------------------

    private function dbValue(DataTypeInterface $property)
    {
        if (is_scalar($property->value())) {
            return $property->value();
        } elseif (method_exists($property->value(), 'putInto')) {
            $container = new self(get_class($property->value()), $this->db, null);
            return $property->value()->putInto($container);
        } else {
            return null;
        }
    }

    private function begin()
    {
        if (!is_null($this->id)) {
            return $this->db->fetchAssoc("SELECT * FROM {$this->table} WHERE id=?", array($this->id));
        }
        return array();
    }

    private function commit(array $row)
    {
        if (is_null($this->id)) {
            $this->db->insert($this->table, $row);
            $this->id = $this->db->lastInsertId();
        } else {
            $this->db->update($this->table, $row, array('id' => $this->id));
        }
        return $this->id;
    }

    private static function convertToTableName($modelClassName)
    {
        return strtolower(str_replace('\\', '_', $modelClassName));
    }
}
