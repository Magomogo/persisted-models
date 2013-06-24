<?php
namespace Test\ObjectMother;

use Test\Company\Model;
use Test\Company\Properties;

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
