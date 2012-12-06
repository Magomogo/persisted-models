<?php
namespace Company;
use Model\ContainerReadyInterface;
use Model\DataContainer\ContainerInterface;

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
