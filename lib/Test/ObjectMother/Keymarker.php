<?php
namespace Test\ObjectMother;

use Model\PropertyContainer\ArrayMap;
use Keymarker\Properties;
use Keymarker\Model;

class Keymarker
{
    public static function friend($id = null)
    {
        $container = new ArrayMap(array(
            'title' => 'Friend',
            'created' => new \DateTime('2012-12-08T10:16+07:00'),
        ));

        return new Model($container->loadProperties(new Properties($id)));
    }

    public static function IT($id = null)
    {
        $container = new ArrayMap(array(
            'title' => 'IT',
            'created' => new \DateTime('2012-12-08T10:36+07:00'),
        ));

        return new Model($container->loadProperties(new Properties($id)));
    }

}
