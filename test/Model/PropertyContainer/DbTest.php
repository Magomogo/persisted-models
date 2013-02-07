<?php
namespace Magomogo\Model\PropertyContainer;

use Mockery as m;
use Test\ObjectMother\Person as TestPerson;
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
        $db->shouldReceive('insert')->with('person', typeOf('array'))->once();
        $db->shouldReceive('insert')->with('credit_card', typeOf('array'))->once();
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

        $properties = new PropertyBag(
            __CLASS__,
            1,
            array(),
            array(
                'ref1' => new PropertyBag(__CLASS__),
                'ref2' => new PropertyBag(__CLASS__),
            )
        );

        self::container($db)->loadProperties($properties);

        $this->assertEquals(4, $properties->reference('ref1')->id);
        $this->assertEquals(5, $properties->reference('ref2')->id);
    }

    public function testSavesReferencesAsForeignKeys()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('properties_type_1',
            array(
                'ref1' => 4,
                'ref2' => 5,
            )
        )->once();
        $db->shouldIgnoreMissing();

        self::container($db)->saveProperties(
            new PropertyBag(
                'properties_type_1',
                null,
                array(),
                array(
                    'ref1' => new PropertyBag(__CLASS__, 4),
                    'ref2' => new PropertyBag(__CLASS__, 5)
                )
            )
        );
    }

    public function testExceptionOnLoadingWhenPropertiesAreNotFound()
    {
        $this->setExpectedException('Magomogo\\Model\\Exception\\NotFound');
        self::container(m::mock(array('fetchAssoc' => false)))->loadProperties(new PropertyBag(__CLASS__, 1));
    }

    public function testCanDeleteProperties()
    {
        $db = m::mock();
        $db->shouldReceive('delete')->with('test_type_1', array('id' => 3))->once();
        $db->shouldReceive('delete')->with('test_type_2', array('id' => 45))->once();

        self::container($db)->deleteProperties(
            array(
                new PropertyBag('test_type_1', 3),
                new PropertyBag('test_type_2', 45)
            )
        );
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

