<?php
namespace Magomogo\Model\PropertyContainer;
use Magomogo\Model\PropertyBag;

interface ContainerInterface
{
    /**
     * @param \Magomogo\Model\PropertyBag $propertyBag
     * @param array $references
     * @return \Magomogo\Model\PropertyBag $propertyBag loaded with data
     */
    public function loadProperties($propertyBag, array $references = array());

    /**
     * @param \Magomogo\Model\PropertyBag $propertyBag
     * @param array $references array of PropertyBag
     * @return \Magomogo\Model\PropertyBag
     */
    public function saveProperties($propertyBag, array $references = array());

    /**
     * @param array $propertyBags array of \Magomogo\Model\PropertyBag
     * @return void
     */
    public function deleteProperties(array $propertyBags);

    /**
     * @param string $referenceName
     * @param \Magomogo\Model\PropertyBag $leftProperties
     * @param array $connections array of \Magomogo\Model\PropertyBag
     * @return void
     */
    public function referToMany($referenceName, $leftProperties, array $connections);

    /**
     * @param string $referenceName
     * @param \Magomogo\Model\PropertyBag $leftProperties
     * @param string $rightPropertiesClassName
     * @return array of \Magomogo\Model\PropertyBag
     */
    public function listReferences($referenceName, $leftProperties, $rightPropertiesClassName);
}
