<?php
namespace Model\PropertyContainer;
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

    /**
     * @param string $referenceName
     * @param \Model\PropertyBag $leftProperties
     * @param array $connections array of \Model\PropertyBag
     * @return void
     */
    public function referToMany($referenceName, PropertyBag $leftProperties, array $connections);

    /**
     * @param string $referenceName
     * @param \Model\PropertyBag $leftProperties
     * @param string $rightPropertiesClassName
     * @return array of \Model\PropertyBag
     */
    public function listReferences($referenceName, PropertyBag $leftProperties, $rightPropertiesClassName);
}
