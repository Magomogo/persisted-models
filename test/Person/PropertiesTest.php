<?php
namespace Person;
use Person\Container\Form;
use Mockery as m;

class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeLoadedFromAContainer()
    {
        $properties = new Properties();
        $properties->load(self::container());

        $this->assertEquals('John', $properties->firstName);
    }

    public function testCanBeSaved()
    {
        $storage = m::mock('Model\\ContainerInterface');
        $storage->shouldIgnoreMissing();
        $storage->shouldReceive('saveProperties')->with(typeOf('array'))->once();

        self::loadedProperties()->save($storage);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function loadedProperties()
    {
        $properties = new Properties();
        $properties->load(self::container());
        return $properties;
    }

    private static function container()
    {
        return new Form(
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
