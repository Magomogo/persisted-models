<?php
namespace Company;
use Model\ContainerReadyInterface;
use Model\DataContainer\ContainerInterface;
use Model\DataContainer\Db;
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
        $properties = new Properties($id);
        $container->loadProperties($properties);
        return new self($properties);
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
