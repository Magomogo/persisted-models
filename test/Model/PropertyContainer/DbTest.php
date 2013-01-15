<?php
namespace Magomogo\Model\PropertyContainer;
use Mockery as m;
use Test\ObjectMother\Person;
use Magomogo\Model\PropertyBag;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsContainerInterface()
    {
        $this->assertInstanceOf('Magomogo\\Model\\PropertyContainer\\ContainerInterface', self::container());
    }

    public function testFollowsTableNamingConvention()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('person_properties', typeOf('array'))->once();
        $db->shouldReceive('insert')->with('creditcard_properties', typeOf('array'))->once();
        $db->shouldIgnoreMissing();
        Person::maxim()->putIn(self::container($db));
    }

    public function testLoadsReferencesAccordingToReferenceName()
    {
        $db = m::mock();
        $db->shouldReceive('fetchAssoc')->andReturn(
            array('ref1' => 4, 'ref2' => 5),
            array()
        );

        $refs = array(
            'ref1' => new TestType1(null),
            'ref2' => new TestType2(null),
        );
        self::container($db)->loadProperties(new TestType1(1), $refs);

        $this->assertEquals(4, $refs['ref1']->id);
        $this->assertEquals(5, $refs['ref2']->id);
    }

    public function testSavesReferencesAsForeignKeys()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('propertycontainer_testtype1',
            array(
                'ref1' => 4,
                'ref2' => 5,
            )
        )->once();
        $db->shouldIgnoreMissing();

        self::container($db)->saveProperties(
            new TestType1(),
            array(
                'ref1' => new TestType1(4),
                'ref2' => new TestType2(5)
            )
        );
    }

    public function testExceptionOnLoadingWhenPropertiesAreNotFound()
    {
        $this->setExpectedException('Magomogo\\Model\\Exception\\NotFound');
        self::container(m::mock(array('fetchAssoc' => false)))->loadProperties(new TestType1(1));
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function container($db = null)
    {
        return new Db($db ?: m::mock(array('fetchAssoc' => array())), 'Magomogo\\Model\\');
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

