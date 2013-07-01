<?php

namespace Magomogo\Persisted;

interface PossessionInterface
{
    /**
     * @param PropertyBag $properties
     * @param null|string $relationName
     * @return mixed
     */
    public function isOwnedBy($properties, $relationName = null);
}