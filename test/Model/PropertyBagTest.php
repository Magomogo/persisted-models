<?php
namespace Model;
use Mockery as m;

class PropertyBagTest extends \PHPUnit_Framework_TestCase
{
    public function testIsIterable()
    {
        $names = array();
        $properties = array();
        foreach (self::bag() as $name => $property) {
            $names[] = $name;
            $properties[] = $property;
        }

        $this->assertEquals(array('title', 'description', 'object'), $names);
        $this->assertEquals(array('default title', 'default descr', new \stdClass()), $properties);
    }

    public function testIdIsNullInitially()
    {
        $this->assertNull(self::bag()->id);
    }

    public function testPersistedMessageSetsId()
    {
        $bag = self::bag();
        $bag->persisted('888', m::mock('Model\\PropertyContainer\\ContainerInterface'));
        $this->assertEquals('888', $bag->id);
    }

    public function testKnowsItsOrigin()
    {
        $properties = self::bag();
        $container = new \Model\PropertyContainer\ArrayMap(array());
        $container->loadProperties($properties);

        $this->assertSame($properties, $properties->assertOriginIs($container));
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function bag()
    {
        $bag = new PropertyBag(array(
            'title' => 'default title',
            'description' => 'default descr',
            'object' => new \stdClass()
        ));
        return $bag;
    }

}
