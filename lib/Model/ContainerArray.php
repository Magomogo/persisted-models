<?php
namespace Model;
use Model\DataType\DataTypeInterface;

class ContainerArray implements ContainerInterface
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

    public function loadProperties(array $properties)
    {
        /** @var DataTypeInterface $property */
        foreach ($properties as $name => $property) {
            $property->setValue($this->nameToValueMap[$name]);
        }

        return $this;
    }

    public function saveProperties(array $properties)
    {
        $this->nameToValueMap = array();

        /** @var DataTypeInterface $property */
        foreach ($properties as $name => $property) {
            $this->nameToValueMap[$name] = $property->value();
        }

        return null;
    }
}
