<?php

namespace Magomogo\Persisted;

use Magomogo\Persisted\Test\Keymarker;
use Mockery as m;

class PropertyBagCollectionTest extends \PHPUnit_Framework_TestCase
{

    public function testCountable()
    {
        $this->assertCount(0, new TestCollection());
    }

    public function testArrayAccess()
    {
        $collection = self::loadedCollection();
        $this->assertNotNull($collection[0]);
        $this->assertNotNull($collection[1]);
        $this->assertArrayNotHasKey(2, $collection);
    }

    public function testConstructsModelOnOffsetGet()
    {
        $collection = self::loadedCollection();
        $this->assertInstanceOf('Magomogo\\Persisted\\Test\\Keymarker\\Model', $collection[0]);
    }

    public function testCanBeAppendedByAModelStoringItsProperties()
    {
        $collection = new TestCollection;
        $collection[] = Test\ObjectMother\Keymarker::IT();

        $this->assertEquals(Test\ObjectMother\Keymarker::IT(), $collection[0]);

        $container = m::mock();
        $container
            ->shouldReceive('referToMany')
            ->with(
                anything(),
                anything(),
                m::on(function($arg) {
                    return (count($arg) == 1) && ($arg[0] instanceof Keymarker\Properties);
                }))
            ->once();
        $collection->putIn($container, m::mock());
    }

    /**
     * @return TestCollection
     */
    private static function loadedCollection()
    {
        $collection = new TestCollection();
        $collection->loadFrom(
            m::mock(
                array(
                    'listReferences' => array(new Keymarker\Properties, new Keymarker\Properties)
                )
            ),
            m::mock()
        );
        return $collection;
    }
}

//======================================================================================================================

class TestCollection extends PropertyBagCollection {

    protected function constructModel($propertyBag)
    {
        return new Keymarker\Model($propertyBag);
    }
}