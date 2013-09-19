<?php

namespace Magomogo\Persisted\Container\SqlDb;

use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\AbstractCollection;

interface NamesInterface
{
    /**
     * @param AbstractProperties $propertyBag
     * @return string
     */
    public function propertyBagToName($propertyBag);

    /**
     * @param AbstractCollection $propertyBagCollection
     * @return string
     */
    public function propertyBagCollectionToName($propertyBagCollection);

    /**
     * @param string $name
     * @return AbstractProperties
     */
    public function nameToPropertyBag($name);

    public function manyToManyRelationName($propertyBagCollection, $ownerPropertyBag);
}