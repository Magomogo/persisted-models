<?php

namespace Magomogo\Persisted\Collection;

interface MemberInterface
{
    /**
     * @param AbstractCollection $collection
     * @param string|null $offset
     */
    public function appendToCollection($collection, $offset = null);
}