<?php
namespace Person;
use Person\Properties;
use Model\PropertyContainer\ArrayMap;
use Mockery as m;

class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeLoadedFromAContainer()
    {
        $properties = self::container()->loadProperties(new Properties());
        $this->assertEquals('John', $properties->firstName);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function container()
    {
        return new ArrayMap(
            array(
                'title' => 'Mr.',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
            )
        );
    }
}
