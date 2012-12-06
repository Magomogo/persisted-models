<?php
namespace Test\ObjectMother;
use Model\DataContainer\ArrayMap;
use Company\Properties;
use Company\Model;

class Company
{
    public static function xiag()
    {
        $container = new ArrayMap(array(
            'name' => 'XIAG',
        ));

        return new Model($container->loadProperties(new Properties()));
    }
}
