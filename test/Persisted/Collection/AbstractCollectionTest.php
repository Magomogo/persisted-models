<?php

namespace Magomogo\Persisted;

use Magomogo\Persisted\Test\Keymarker;
use Mockery as m;

class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
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

    public function testAskingAnUnknownCollectionName()
    {
        $this->setExpectedException('Magomogo\\Persisted\\Exception\\CollectionName');
        $collection = new TestCollection();
        $collection->name();
    }

    public function testACollectionCanOptionallyHaveAName()
    {
        $collection = new TestCollection();
        $collection->name('tags');
        $this->assertSame('tags', $collection->name());
    }

    public function testCanReturnAllStoredProperties()
    {
        $this->assertEquals(
            array(new Keymarker\Properties, new Keymarker\Properties),
            self::loadedCollection()->allProperties()
        );
    }

    public function testDoesntExposePropertiesInstance()
    {
        $collection = self::loadedCollection();
        $p1 = $collection->allProperties();
        $p2 = $collection->allProperties();

        $this->assertNotSame($p1[0], $p2[0]);
    }

//----------------------------------------------------------------------------------------------------------------------

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

class TestCollection extends Collection\AbstractCollection {

    protected function constructModel($properties)
    {
        return new Keymarker\Model($properties);
    }

    public function constructProperties()
    {
        return new Keymarker\Properties();
    }
}
