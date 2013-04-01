<?php
namespace Magomogo\Persisted\PropertyContainer;

use Mockery as m;
use Test\ObjectMother\Person as TestPerson;
use Magomogo\Persisted\PropertyBag;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsContainerInterface()
    {
        $this->assertInstanceOf('Magomogo\\Persisted\\PropertyContainer\\ContainerInterface', self::container());
    }

    public function testFollowsTableNamingConvention()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('test_person_properties', typeOf('array'))->once();
        $db->shouldReceive('insert')->with('test_creditcard_properties', typeOf('array'))->once();
        $db->shouldIgnoreMissing();
        TestPerson::maxim()->putIn(self::container($db));
    }

    public function testLoadsReferencesAccordingToReferenceName()
    {
        $db = m::mock();
        $db->shouldReceive('fetchAssoc')->andReturn(
            array('ref1' => 4, 'ref2' => 5),
            array()
        );

        $properties = new TestType3(1);

        self::container($db)->loadProperties($properties);

        $this->assertEquals(4, $properties->foreign()->ref1->id);
        $this->assertEquals(5, $properties->foreign()->ref2->id);
    }

    public function testSavesReferencesAsForeignKeys()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('propertycontainer_testtype3',
            array(
                'ref1' => 4,
                'ref2' => 5,
            )
        )->once();
        $db->shouldIgnoreMissing();

        $properties = new TestType3();
        $properties->foreign()->ref1->persisted(4, $this);
        $properties->foreign()->ref2->persisted(5, $this);

        self::container($db)->saveProperties($properties);
    }

    public function testExceptionOnLoadingWhenPropertiesAreNotFound()
    {
        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        self::container(m::mock(array('fetchAssoc' => false)))->loadProperties(new TestType1(1));
    }

    public function testCanDeleteProperties()
    {
        $db = m::mock();
        $db->shouldReceive('delete')->with('propertycontainer_testtype1', array('id' => 3))->once();
        $db->shouldReceive('delete')->with('propertycontainer_testtype2', array('id' => 45))->once();

        self::container($db)->deleteProperties(
            array(
                new TestType1(3),
                new TestType2(45)
            )
        );
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function container($db = null)
    {
        return new Db($db ?: m::mock(array('fetchAssoc' => array())), 'Magomogo\\Persisted\\');
    }
}


class TestType1 extends PropertyBag
{
    protected function properties()
    {
        return array();
    }
}

class TestType2 extends PropertyBag
{
    protected function properties()
    {
        return array();
    }
}


class TestType3 extends PropertyBag
{
    protected function properties()
    {
        return array();
    }

    protected function foreigners()
    {
        return array(
            'ref1' => new TestType1(null),
            'ref2' => new TestType2(null),
        );
    }
}

