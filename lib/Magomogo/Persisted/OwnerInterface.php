<?php

namespace Magomogo\Persisted;

interface OwnerInterface 
{

    public function isOwner($properties);
}