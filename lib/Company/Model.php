<?php
namespace Company;
use Model\ContainerReadyInterface;
use Model\PropertyContainer\ContainerInterface;
use Model\PropertyContainer\Db;
use Employee;
use Person;

class Model implements ContainerReadyInterface
{
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

    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->properties)->id;
    }

    public function propertiesFrom(ContainerInterface $container)
    {
        return $this->properties->assertOriginIs($container);
    }
}
