<?php
namespace Test\ObjectMother;

use Test\Keymarker\Model;
use Test\Keymarker\Properties;

class Keymarker
{
    public static function friend()
    {
        return new Model(
            new Properties(array('id' => 'Friend', 'created' => new \DateTime('2012-12-08T10:16+07:00')))
        );
    }

    public static function IT()
    {
        return new Model(
            new Properties(array('id' => 'IT', 'created' => new \DateTime('2012-12-08T10:36+07:00')))
        );
    }

}
