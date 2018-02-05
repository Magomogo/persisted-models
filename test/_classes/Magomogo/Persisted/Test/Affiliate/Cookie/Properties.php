<?php

namespace Magomogo\Persisted\Test\Affiliate\Cookie;

use Magomogo\Persisted\AbstractProperties;

/**
 * @property integer $lifeTime
 * @property boolean $isMaster
 * @property boolean $shouldBeKept
 */
class Properties extends AbstractProperties
{
    public $id;
    public $lifeTime = 0;

    public function naturalKeyFieldName()
    {
        return 'id';
    }
}
