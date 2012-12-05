<?php
namespace Person\Container;
use Model\DataType\DataTypeInterface;

class Form implements \Model\ContainerInterface
{
    /**
     * @var array
     */
    private $nameToValueMap;

    /**
     * @param array $nameToValueMap;
     */
    public function __construct(array $nameToValueMap)
    {
        $this->nameToValueMap = $nameToValueMap;
    }

    /**
     * @param string $uniqueKey
     * @return self
     */
    public function begin($uniqueKey = null)
    {
        return $this;
    }

    /**
     * @param $name
     * @param DataTypeInterface $property
     * @return self
     */
    public function loadProperty($name, DataTypeInterface $property)
    {
        $property->setValue($this->nameToValueMap[$name]);
        return $this;
    }

    /**
     * @param $name
     * @param DataTypeInterface $property
     * @return self
     */
    public function saveProperty($name, DataTypeInterface $property)
    {
        $this->nameToValueMap[$name] = $property->value();
        return $this;
    }

    /**
     * @return string unique key
     */
    public function commit()
    {
        return null;
    }
}
