<?php
namespace Test\ObjectMother;

use Company\Model;

class Company
{
    public static function xiag($id = null)
    {
        return new Model(Model::propertiesSample($id, array('name' => 'XIAG')));
    }

    public static function nstu($id = null)
    {
        return new Model(Model::propertiesSample($id, array('name' => 'NSTU')));
    }
}
