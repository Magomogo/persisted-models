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
        /** @var DataTypeInterface $property */
        foreach ($properties as $name => $property) {
            $this->nameToValueMap[$name] = $property->value();
        }

        return null;
    }
}
