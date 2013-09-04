<?php

namespace Magomogo\Persisted\Container\Db;

use Magomogo\Persisted\PropertyBag;

interface NamesInterface
{
    /**
     * @param PropertyBag $propertyBag
     * @return string
     */
    public function containmentTableName($propertyBag);

    /**
     * @param PropertyBag $propertyBag
     * @return string
     */
    public function referencedColumnName($propertyBag);
}