<?php
namespace Test\ObjectMother;

use Company\Properties;
use Company\Model;

class Company
{
    public static function xiag($id = null)
    {
        $properties = new Properties($id);
        $properties->name = 'XIAG';

        return new Model($properties);
    }

    public static function nstu($id = null)
    {
        $properties = new Properties($id);
        $properties->name = 'NSTU';

        return new Model($properties);
    }
}
