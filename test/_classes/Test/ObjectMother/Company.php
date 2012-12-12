<?php
namespace Test\ObjectMother;
use Magomogo\Model\PropertyContainer\ArrayMap;
use Company\Properties;
use Company\Model;

class Company
{
    public static function xiag($id = null)
    {
        $container = new ArrayMap(array(
            'name' => 'XIAG',
        ));

        return new Model($container->loadProperties(new Properties($id)));
    }

    public static function nstu($id = null)
    {
        $container = new ArrayMap(array(
            'name' => 'NSTU',
        ));

        return new Model($container->loadProperties(new Properties($id)));
    }
}
