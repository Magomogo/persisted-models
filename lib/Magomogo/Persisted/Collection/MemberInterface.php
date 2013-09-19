<?php

namespace Magomogo\Persisted\Collection;

interface MemberInterface
{
    /**
     * @param AbstractCollection $collection
     * @param $offset
     */
    public function appendToCollection($collection, $offset);
}