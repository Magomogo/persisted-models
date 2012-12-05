<?php
namespace Model;

class PropertyBag implements \Iterator
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

    public function current()
    {
        return current($this->nameToDataMap);
    }

    public function next()
    {
        next($this->nameToDataMap);
    }

    public function key()
    {
        return key($this->nameToDataMap);
    }

    public function valid()
    {
        $key = key($this->nameToDataMap);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

    public function rewind()
    {
        reset($this->nameToDataMap);
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
