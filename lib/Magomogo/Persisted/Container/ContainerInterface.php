<?php
namespace Magomogo\Persisted\Container;
use Magomogo\Persisted\PropertyBagCollection;
use Magomogo\Persisted\PropertyBag;

interface ContainerInterface
{
    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag $propertyBag loaded with data
     */
    public function loadProperties($propertyBag);

    /**
     * @param \Magomogo\Persisted\PropertyBag $propertyBag
     * @return \Magomogo\Persisted\PropertyBag
     */
    public function saveProperties($propertyBag);

    /**
     * @param array $propertyBags array of \Magomogo\Model\PropertyBag
     * @return void
     */
    public function deleteProperties(array $propertyBags);

    /**
     * @param PropertyBagCollection $collectionBag
     * @param \Magomogo\Persisted\CollectionOwnerInterface $leftProperties
     * @param array $connections array of \Magomogo\Model\PropertyBag
     * @return void
     */
    public function referToMany($collectionBag, $leftProperties, array $connections);

    /**
     * @param PropertyBagCollection $collectionBag
     * @param \Magomogo\Persisted\CollectionOwnerInterface $leftProperties
     * @return array of \Magomogo\Model\PropertyBag
     */
    public function listReferences($collectionBag, $leftProperties);
}
