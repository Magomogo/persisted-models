<?php
namespace Test\ObjectMother;

use Company\Model;
use Company\Properties;

class Company
{
    public static function xiag($id = null)
    {
        return new Model(new Properties($id, array('name' => 'XIAG')));
    }

    public static function nstu($id = null)
    {
        return new Model(new Properties($id, array('name' => 'NSTU')));
    }
}
