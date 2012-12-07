<?php
namespace Model\DataContainer;
use Model\PropertyBag;

interface ContainerInterface
{
    /**
     * @param \Model\PropertyBag $propertyBag
     * @return array of references
     */
    public function loadProperties(PropertyBag $propertyBag);

    /**
     * @param \Model\PropertyBag $propertyBag
     * @return \Model\PropertyBag
     */
    public function saveProperties(PropertyBag $propertyBag);
}
