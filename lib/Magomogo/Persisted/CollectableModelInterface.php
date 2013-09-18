<?php

namespace Magomogo\Persisted;

interface CollectableModelInterface
{
    /**
     * @param PropertyBagCollection $collection
     * @param $offset
     */
    public function appendToCollection($collection, $offset);
}