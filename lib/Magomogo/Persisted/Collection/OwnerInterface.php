<?php

namespace Magomogo\Persisted\Collection;

use Magomogo\Persisted\PropertiesInterface;

interface OwnerInterface extends PropertiesInterface
{
    public function collections();
}