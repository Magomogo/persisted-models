<?php
namespace Model;

/**
 * @property string $id
 */
class PropertyBag implements \IteratorAggregate
{
    private $id;
    private $nameToDataMap;

    public function __construct(array $nameToDataMap, $id = null)
    {
        $this->nameToDataMap = $nameToDataMap;
        $this->id = $id;
    }

    public function __get($name)
    {
        if ($name == 'id') {
            return $this->id;
        }
        return $this->prop($name)->value();
    }

    public function __set($name, $value)
    {
        $this->prop($name)->setValue($value);
    }

    public function persisted($id)
    {
        $this->id = $id;
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
