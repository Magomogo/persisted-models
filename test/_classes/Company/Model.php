<?php
namespace Company;
use Magomogo\Model\ContainerReadyAbstract;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Employee;
use Person;

class Model extends ContainerReadyAbstract
{
    public static function loadFrom(ContainerInterface $container, $id)
    {
        return new self($container->loadProperties(new Properties($id)));
    }

    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    public function name()
    {
        return $this->properties->name;
    }
}
