<?php
namespace Test\ObjectMother;

use Model\DataContainer\ArrayMap;
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

}
