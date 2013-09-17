<?php

namespace Magomogo\Persisted;

interface PossessionInterface
{
    /**
     * @return \stdClass $relationName => Properties
     */
    public function foreign();

    /**
     * @param PropertyBag $properties
     * @param null|string $relationName
     * @return mixed
     */
    public function ownedBy($properties, $relationName = null);
}