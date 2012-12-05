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
            $property->setValue(array_key_exists($name, $this->nameToValueMap) ? $this->nameToValueMap[$name] : null);
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
