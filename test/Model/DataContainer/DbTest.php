<?php
namespace Model\DataContainer;
use Mockery as m;
use Test\ObjectMother\Person;
use Model\PropertyBag;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsContainerInterface()
    {
        $this->assertInstanceOf('Model\\DataContainer\\ContainerInterface', self::container());
    }

    public function testFollowsTableNamingConvention()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('person_properties', typeOf('array'))->once();
        $db->shouldReceive('insert')->with('creditcard_properties', typeOf('array'))->once();
        $db->shouldIgnoreMissing();
        Person::maxim()->putIn(self::container($db));
    }

    public function testLoadsReferencesAccordingToColumnNamesConvention()
    {
        $db = m::mock();
        $db->shouldReceive('fetchAssoc')->andReturn(
            array('model_datacontainer_testtype1' => 4, 'model_datacontainer_testtype2' => 5),
            array()
        );

        $refs = self::container($db)->loadProperties(new PropertyBag(array(), 1));

        $this->assertInstanceOf('Model\\Datacontainer\\TestType1', $refs['model_datacontainer_testtype1']);
        $this->assertInstanceOf('Model\\Datacontainer\\TestType2', $refs['model_datacontainer_testtype2']);
    }

    public function testSavesReferencesAsForeignKeys()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('model_propertybag',
            array(
                'model_datacontainer_testtype1' => 4,
                'model_datacontainer_testtype2' => 5,
            )
        )->once();
        $db->shouldIgnoreMissing();

        self::container($db)->saveProperties(new PropertyBag(array()), array(new TestType1(4), new TestType2(5)));
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function container($db = null)
    {
        return new Db($db ?: m::mock());
    }
}


class TestType1 extends PropertyBag
{
    public function __construct($id)
    {
        parent::__construct(array(), $id);
    }
}

class TestType2 extends PropertyBag
{
    public function __construct($id)
    {
        parent::__construct(array(), $id);
    }
}

