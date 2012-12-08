<?php
namespace Model\DataContainer;
use Model\PropertyBag;

interface ContainerInterface
{
    /**
     * @param \Model\PropertyBag $propertyBag
     * @return array of PropertyBag of references
     */
    public function loadProperties(PropertyBag $propertyBag);

    /**
     * @param \Model\PropertyBag $propertyBag
     * @param array $references array of PropertyBag
     * @return \Model\PropertyBag
     */
    public function saveProperties(PropertyBag $propertyBag, array $references = array());

    /**
     * @param \Model\PropertyBag $leftProperties
     * @param array $connections array of \Model\PropertyBag
     * @return void
     */
    public function connectToMany(PropertyBag $leftProperties, array $connections);

    /**
     * @param \Model\PropertyBag $leftProperties
     * @param \Model\PropertyBag $rightPropertiesType
     * @return array of \Model\PropertyBag
     */
    public function listConnections(PropertyBag $leftProperties, PropertyBag $rightPropertiesType);
}
