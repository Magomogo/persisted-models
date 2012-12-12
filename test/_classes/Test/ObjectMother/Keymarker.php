<?php
namespace Test\ObjectMother;

use Magomogo\Model\PropertyContainer\ArrayMap;
use Keymarker\Properties;
use Keymarker\Model;

class Keymarker
{
    public static function friend()
    {
        $container = new ArrayMap(array(
            'created' => new \DateTime('2012-12-08T10:16+07:00'),
        ));

        return new Model($container->loadProperties(new Properties('Friend')));
    }

    public static function IT()
    {
        $container = new ArrayMap(array(
            'created' => new \DateTime('2012-12-08T10:36+07:00'),
        ));

        return new Model($container->loadProperties(new Properties('IT')));
    }

}
