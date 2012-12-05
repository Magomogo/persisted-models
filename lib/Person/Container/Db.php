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
     * @var string
     */
    private $uniqueKey;

    /**
     * @var array
     */
    private $row = array();

    /**
     * @param Connection $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param string $uniqueKey
     * @return self
     */
    public function begin($uniqueKey = null)
    {
        if (!is_null($uniqueKey)) {
            $this->row = $this->db->fetchAssoc('SELECT * FROM person WHERE id=?', array($uniqueKey));
        } else {
            $this->row = array();
        }
        $this->uniqueKey = $uniqueKey;
        return $this;
    }

    public function loadProperty($name, DataTypeInterface $property)
    {
        $property->setValue($this->row[$name]);
        return $this;
    }

    public function saveProperty($name, DataTypeInterface $property)
    {
        $this->row[$name] = $property->value();
        return $this;
    }

    public function commit()
    {
        if (is_null($this->uniqueKey)) {
            $this->db->insert('person', $this->row);
            $this->uniqueKey = $this->db->lastInsertId();
        }
        $this->db->update('person', $this->row, array('id' => $this->uniqueKey));
        return $this->uniqueKey;
    }
}
