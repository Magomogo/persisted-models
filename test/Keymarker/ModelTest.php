<?php
namespace Keymarker;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeRepresentedAsAString()
    {
        $this->assertEquals('Friend', strval(self::keymarker()));
    }

    private static function keymarker()
    {
        $properties = new Properties('Friend');
        $properties->created = new \DateTime('2012-12-08 09:50');
        return new Model($properties);
    }
}
