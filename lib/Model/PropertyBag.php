<?php
namespace Model;

class PropertyBag implements \IteratorAggregate
{
    private $nameToDataMap;

    public function __construct(array $nameToDataMap)
    {
        $this->nameToDataMap = $nameToDataMap;
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
