<?php
namespace Model\DataContainer;
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

    public function loadProperties(PropertyBag $propertyBag, array $references = array())
    {
        foreach ($propertyBag as $name => &$property) {
            $property = array_key_exists($name, $this->nameToValueMap) ? $this->nameToValueMap[$name] : null;
        }
        $propertyBag->persisted(null, $this);
        return $propertyBag;
    }

    /**
     * @param \Model\PropertyBag $propertyBag
     * @param array $references
     * @return \Model\PropertyBag
     */
    public function saveProperties(PropertyBag $propertyBag, array $references = array())
    {
        $this->nameToValueMap = array();

        foreach ($propertyBag as $name => $property) {
            $this->nameToValueMap[$name] = $property;
        }
        $propertyBag->persisted(null, $this);
        return $propertyBag;
    }
}
