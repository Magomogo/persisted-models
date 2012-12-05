<?php
namespace Person\DataSource;

class Db implements \Model\DataSourceInterface
{
    private $row;

    /**
     * @param integer $id
     * @param \PDO $pdo
     */
    public function __construct($id, $pdo)
    {
        $stm = $pdo->prepare('SELECT * FROM person WHERE id=?');
        $stm->execute(array($id));
        $this->row = $stm->fetch(\PDO::FETCH_ASSOC);
    }

    public function readValue($propertyName)
    {
        return $this->row[$propertyName];
    }
}
