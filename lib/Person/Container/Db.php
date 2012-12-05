<?php
namespace Person\Container;
use Model\DataType\DataTypeInterface;
use \Doctrine\DBAL\Connection;

class Db implements \Model\ContainerInterface
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @var string|null
     */
    private $id;

    /**
     * @param Connection $db
     * @param $id
     */
    public function __construct($db, $id = null)
    {
        $this->id = $id;
        $this->db = $db;
    }

    public function loadProperties(array $properties)
    {
        $row = $this->begin();

        /** @var DataTypeInterface $property */
        foreach ($properties as $name => $property) {
            $property->setValue($row[$name]);
        }
        return $this;
    }

    public function saveProperties(array $properties)
    {
        $row = array();
        /** @var DataTypeInterface $property */
        foreach ($properties as $name => $property) {
            $row[$name] = $property->value();
        }

        return $this->commit($row);
    }

//----------------------------------------------------------------------------------------------------------------------

    private function begin()
    {
        if (!is_null($this->id)) {
            return $this->db->fetchAssoc('SELECT * FROM person WHERE id=?', array($this->id));
        }
        return array();
    }

    private function commit(array $row)
    {
        if (is_null($this->id)) {
            $this->db->insert('person', $row);
            $this->id = $this->db->lastInsertId();
        } else {
            $this->db->update('person', $row, array('id' => $this->id));
        }
        return $this->id;
    }

}
