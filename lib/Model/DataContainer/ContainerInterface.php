<?php
namespace Model\DataContainer;
use Model\PropertyBag;

interface ContainerInterface
{
    /**
     * @param \Model\PropertyBag $propertyBag
     * @param array $references
     * @return \Model\PropertyBag $propertyBag loaded with data
     */
    public function loadProperties(PropertyBag $propertyBag, array $references = array());

    /**
     * @param \Model\PropertyBag $propertyBag
     * @param array $references array of PropertyBag
     * @return \Model\PropertyBag
     */
    public function saveProperties(PropertyBag $propertyBag, array $references = array());
}
