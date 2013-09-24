<?php

namespace Magomogo\Persisted\Container\SqlDb;

use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Collection;

interface NamesInterface
{
    /**
     * @param AbstractProperties $properties
     * @return string
     */
    public function propertiesToName($properties);

    /**
     * @param Collection\AbstractCollection $collection
     * @return string
     */
    public function collectionToName($collection);

    /**
     * @param Collection\AbstractCollection $collection
     * @param AbstractProperties $ownerProperties
     * @return string
     */
    public function manyToManyRelationName($collection, $ownerProperties);
}
