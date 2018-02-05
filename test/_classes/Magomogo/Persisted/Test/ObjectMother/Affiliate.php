<?php

namespace Magomogo\Persisted\Test\ObjectMother;

use Magomogo\Persisted\Test\Affiliate\Model;
use Magomogo\Persisted\Test\Affiliate\Properties;
use Magomogo\Persisted\Test\Affiliate\Cookie;

class Affiliate
{
    public static function sts()
    {
        $cookie = new Cookie\Model(new Cookie\Properties(array(
            'lifeTime' => 60
        )));

        return new Model(
            new Properties(array(
                'name' => 'STS Shop',
                'cookie' => $cookie
            ))
        );
    }
}
