<?php

namespace Magomogo\Persisted\Test\Keymarker;

use Magomogo\Persisted\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @param Properties $properties
     * @return Model
     */
    protected function constructModel($properties)
    {
        return new Model($properties);
    }

    public function constructProperties()
    {
        return new Properties();
    }
}
