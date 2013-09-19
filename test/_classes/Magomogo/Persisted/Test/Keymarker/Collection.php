<?php

namespace Magomogo\Persisted\Test\Keymarker;

use Magomogo\Persisted\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @param Properties $propertyBag
     * @return Model
     */
    protected function constructModel($propertyBag)
    {
        return new Model($propertyBag);
    }
}