<?php
namespace Test\ObjectMother;

use Keymarker\Properties;
use Keymarker\Model;

class Keymarker
{
    public static function friend()
    {
        $properties = new Properties('Friend');
        $properties->created = new \DateTime('2012-12-08T10:16+07:00');

        return new Model($properties);
    }

    public static function IT()
    {
        $properties = new Properties('IT');
        $properties->created = new \DateTime('2012-12-08T10:36+07:00');

        return new Model($properties);
    }

}
