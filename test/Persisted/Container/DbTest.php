<?php
namespace Magomogo\Persisted\Container;

use Magomogo\Persisted\PossessionInterface;
use Mockery as m;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\ObjectMother\Person as TestPerson;
use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\Test\Person;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsContainerInterface()
    {
        $this->assertInstanceOf('Magomogo\\Persisted\\Container\\ContainerInterface', self::container());
    }

    public function testFollowsTableNamingConvention()
    {
        $db = self::dbMock();
        $db->shouldReceive('insert')->with('person', typeOf('array'))->once();
        $db->shouldReceive('insert')->with('creditcard', typeOf('array'))->once();
        $properties = new Person\Properties;
        $properties->putIn(self::container($db));
    }

    public function testLoadsReferencesAccordingToReferenceName()
    {
        $db = self::dbMock();
        $db->shouldReceive('fetchAssoc')->andReturn(
            array('ref1' => 4, 'ref2' => 5),
            array()
        );

        $properties = new TestType3();
        $properties->persisted(1, self::container($db));

        self::container($db)->loadProperties($properties);

        $this->assertEquals(4, $properties->foreign()->ref1->id(self::container($db)));
        $this->assertEquals(5, $properties->foreign()->ref2->id(self::container($db)));
    }

    public function testSavesReferencesAsForeignKeys()
    {
        $db = self::dbMock();
        $db->shouldReceive('insert')->with('magomogo_persisted_container_testtype3',
            array(
                'ref1' => 4,
                'ref2' => 5,
            )
        )->once();

        $properties = new TestType3();
        $properties->foreign()->ref1->persisted(4, self::container($db));
        $properties->foreign()->ref2->persisted(5, self::container($db));

        self::container($db)->saveProperties($properties);
    }

    public function testExceptionOnLoadingWhenPropertiesAreNotFound()
    {
        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');

        $container = self::container(m::mock(array('fetchAssoc' => false, 'quoteIdentifier' => 'table')));

        $notFoundProperties = new TestType1();
        $notFoundProperties->persisted(11, $container);

        $container->loadProperties($notFoundProperties);
    }

    public function testExceptionOnLoadingWhenIdInThisContainerIsUndefined()
    {
        $this->setExpectedException('Magomogo\\Persisted\\Exception\\NotFound');
        self::container()->loadProperties(new Person\Properties);
    }

    public function testCanDeleteProperties()
    {
        $db = self::dbMock();
        $db->shouldReceive('delete')->with('magomogo_persisted_container_testtype1', array('id' => 3))->once();
        $db->shouldReceive('delete')->with('magomogo_persisted_container_testtype2', array('id' => 45))->once();

        $persistedProperties1 = new TestType1();
        $persistedProperties1->persisted(3, self::container($db));
        $persistedProperties2 = new TestType2();
        $persistedProperties2->persisted(45, self::container($db));

        self::container($db)->deleteProperties(
            array(
                $persistedProperties1,
                $persistedProperties2
            )
        );
    }

    private static function dbMock()
    {
        $db = m::mock();
        $db->shouldReceive('quoteIdentifier')->andReturnUsing(function($arg) {return $arg;});
        $db->shouldIgnoreMissing();
        return $db;
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function container($db = null)
    {
        return new Db($db ?: m::mock(array('fetchAssoc' => array())), new DbNames());
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


class TestType3 extends PropertyBag implements PossessionInterface
{
    private $ref1;
    private $ref2;

    public function __construct($valuesToSet = null)
    {
        parent::__construct($valuesToSet);
        $this->ref1 = new TestType1(null);
        $this->ref2 = new TestType2(null);
    }

    protected function properties()
    {
        return array();
    }

    /**
     * @return \stdClass $relationName => Properties
     */
    public function foreign()
    {
        $f = new \stdClass();
        $f->ref1 = $this->ref1;
        $f->ref2 = $this->ref2;
        return $f;
    }

    /**
     * @param PropertyBag $properties
     * @param null|string $relationName
     * @return mixed
     */
    public function isOwnedBy($properties, $relationName = null)
    {
        // TODO: Implement isOwnedBy() method.
    }
}

