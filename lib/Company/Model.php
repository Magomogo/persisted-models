<?php
namespace Company;
use Model\ContainerReadyInterface;
use Model\PropertyContainer\ContainerInterface;
use Model\PropertyContainer\Db;
use Employee;
use Person;

class Model implements ContainerReadyInterface
{
    use \Model\ContainerUtils;

    /**
     * @var Properties
     */
    private $properties;

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
