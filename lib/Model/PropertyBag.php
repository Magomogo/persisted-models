<?php
namespace Model;

class PropertyBag implements \IteratorAggregate
{
    private $nameToDataMap;

    public function __construct(array $nameToDataMap)
    {
        $this->nameToDataMap = $nameToDataMap;
    }

    public function loadFrom(\Model\DataContainer\ContainerInterface $container)
    {
        $container->loadProperties($this);
        return $this;
    }

    public function putIn(\Model\DataContainer\ContainerInterface $container)
    {
        return $container->saveProperties($this);
    }

    public function __get($name)
    {
        return $this->prop($name)->value();
    }

    public function __set($name, $value)
    {
        $this->prop($name)->setValue($value);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->nameToDataMap);
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param $name
     * @return \Model\DataType\DataTypeInterface
     */
    private function prop($name)
    {
        return $this->nameToDataMap[$name];
    }

}
