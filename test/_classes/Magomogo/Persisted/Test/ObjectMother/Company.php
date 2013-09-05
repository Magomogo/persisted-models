<?php
namespace Magomogo\Persisted\Test\ObjectMother;

use Magomogo\Persisted\Test\Company\Model;
use Magomogo\Persisted\Test\Company\Properties;

class Company
{
    public static function xiag($id = null)
    {
        return new Model(new Properties(array('name' => 'XIAG')));
    }

    public static function nstu($id = null)
    {
        return new Model(new Properties(array('name' => 'NSTU')));
    }
}
