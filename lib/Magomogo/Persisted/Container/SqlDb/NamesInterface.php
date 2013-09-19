<?php

namespace Magomogo\Persisted\Container\SqlDb;

use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\AbstractCollection;

interface NamesInterface
{
    /**
     * @param PropertyBag $propertyBag
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
     * @return PropertyBag
     */
    public function nameToPropertyBag($name);

    public function manyToManyRelationName($propertyBagCollection, $ownerPropertyBag);
}