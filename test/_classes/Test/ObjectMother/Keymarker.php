<?php
namespace Test\ObjectMother;

use Keymarker\Model;

class Keymarker
{
    public static function friend()
    {
        return new Model(Model::propertiesSample('Friend', array('created' => new \DateTime('2012-12-08T10:16+07:00'))));
    }

    public static function IT()
    {
        return new Model(Model::propertiesSample('IT', array('created' => new \DateTime('2012-12-08T10:36+07:00'))));
    }

}
