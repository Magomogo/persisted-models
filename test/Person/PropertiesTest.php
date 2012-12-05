<?php
namespace Person;
use Person\Properties;
use Model\DataContainer\ArrayMap;
use Mockery as m;

class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeLoadedFromAContainer()
    {
        $properties = new Properties();
        $properties->loadFrom(self::container());

        $this->assertEquals('John', $properties->firstName);
    }

    public function testCanBeSaved()
    {
        $storage = m::mock('Model\\DataContainer\\ContainerInterface');
        $storage->shouldIgnoreMissing();
        $storage->shouldReceive('saveProperties')->once();

        self::loadedProperties()->putIn($storage);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function loadedProperties()
    {
        $properties = new Properties();
        $properties->loadFrom(self::container());
        return $properties;
    }

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
