<?php
namespace Company;
use Model\ContainerReadyInterface;
use Model\DataContainer\ContainerInterface;
use Doctrine\DBAL\Connection;
use Model\DataContainer\Db;
use Employee;
use Person;

class Model implements ContainerReadyInterface
{
    /**
     * @var Properties
     */
    private $properties;

    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    public function name()
    {
        return $this->properties->name;
    }

    public function getEmployeeById($employeeId, Connection $db)
    {
        $stm = $db->executeQuery(
            'SELECT 1 FROM person_properties WHERE id = :employeeId AND company_id = :companyId',
            array('employeeId' => $employeeId, 'companyId' => $this->id())
        );
        if ($stm->fetchAll()) {
            $container = new Db($db);
            return new Employee\Model($this, $container->loadProperties(new Person\Properties($employeeId)));
        }
        return null;
    }

    public function id()
    {
        return $this->properties->id;
    }

    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->properties)->id;
    }

    public function loadFrom(ContainerInterface $container, $id)
    {
        $this->properties = $container->loadProperties(new Properties($id));
        return $this;
    }
}
