<?php

namespace Magomogo\Persisted\Container\SqlDb;

use Magomogo\Persisted\PropertyBag;

interface NamesInterface
{
    /**
     * @param PropertyBag $propertyBag
     * @return string
     */
    public function classToName($propertyBag);

    /**
     * @param string $name
     * @return PropertyBag
     */
    public function nameToClass($name);

    public function manyToManyRelationName($collectionBag, $ownerPropertyBag);
}