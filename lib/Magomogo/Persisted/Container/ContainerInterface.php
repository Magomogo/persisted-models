<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\Collection;
use Magomogo\Persisted\AbstractProperties;

interface ContainerInterface
{
    /**
     * @param \Magomogo\Persisted\AbstractProperties $propertyBag
     * @return \Magomogo\Persisted\AbstractProperties $propertyBag loaded with data
     */
    public function loadProperties($propertyBag);

    /**
     * @param \Magomogo\Persisted\AbstractProperties $propertyBag
     * @return \Magomogo\Persisted\AbstractProperties
     */
    public function saveProperties($propertyBag);

    /**
     * @param array $propertyBags array of \Magomogo\Model\AbstractProperties
     * @return void
     */
    public function deleteProperties(array $propertyBags);

    /**
     * @param Collection\AbstractCollection $collectionBag
     * @param Collection\OwnerInterface $leftProperties
     * @param array $propertyBags array of \Magomogo\Model\AbstractProperties
     * @return void
     */
    public function referToMany($collectionBag, $leftProperties, array $propertyBags);

    /**
     * @param Collection\AbstractCollection $collectionBag
     * @param Collection\OwnerInterface $leftProperties
     * @return array of \Magomogo\Model\AbstractProperties
     */
    public function listReferences($collectionBag, $leftProperties);
}
