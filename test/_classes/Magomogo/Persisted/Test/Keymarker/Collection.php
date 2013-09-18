<?php

namespace Magomogo\Persisted\Test\Keymarker;

use Magomogo\Persisted\PropertyBagCollection;

class Collection extends PropertyBagCollection
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