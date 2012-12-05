<?php
namespace Model;

class PropertyBag
{
    private $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function load(\Model\DataContainer\ContainerInterface $container)
    {
        $container->loadProperties($this->properties);
        return $this;
    }

    public function save(\Model\DataContainer\ContainerInterface $container)
    {
        return $container->saveProperties($this->properties);
    }

    public function __get($name)
    {
        return $this->prop($name)->value();
    }

    public function __set($name, $value)
    {
        $this->prop($name)->setValue($value);
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param $name
     * @return \Model\DataType\DataTypeInterface
     */
    private function prop($name)
    {
        return $this->properties[$name];
    }
}
