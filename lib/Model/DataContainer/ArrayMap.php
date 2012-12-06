<?php
namespace Model\DataContainer;
use Model\DataType\DataTypeInterface;
use Model\PropertyBag;

class ArrayMap implements ContainerInterface
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

    public function loadProperties(PropertyBag $propertyBag)
    {
        /** @var DataTypeInterface $property */
        foreach ($propertyBag as $name => $property) {
            $property->setValue(array_key_exists($name, $this->nameToValueMap) ? $this->nameToValueMap[$name] : null);
        }

        return $propertyBag;
    }

    public function saveProperties(PropertyBag $propertyBag)
    {
        $this->nameToValueMap = array();

        /** @var DataTypeInterface $property */
        foreach ($propertyBag as $name => $property) {
            $this->nameToValueMap[$name] = $property->value();
        }

        return $propertyBag;
    }
}
