<?php
namespace Magomogo\Persisted\Container;

use Hamcrest\Core\IsTypeOf;
use Magomogo\Persisted\PossessionInterface;
use Mockery as m;
use PHPUnit\Framework\Test;
use Test\ObjectMother\Person as TestPerson;
use Magomogo\Persisted\PropertyBag;
use Test\Person;
use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{

    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testImplementsContainerInterface()
    {
        $this->assertInstanceOf('Magomogo\\Persisted\\Container\\ContainerInterface', self::container());
    }

    public function testFollowsTableNamingConvention()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('test_person_properties', IsTypeOf::typeOf('array'))->once();
        $db->shouldReceive('insert')->with('test_creditcard_properties', IsTypeOf::typeOf('array'))->once();
        $db->shouldIgnoreMissing();
        $properties = new Person\Properties;
        $properties->putIn(self::container($db));
    }

    public function testLoadsReferencesAccordingToReferenceName()
    {
        $db = m::mock();
        $db->shouldReceive('fetchAssociative')->andReturn(
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
        $db = m::mock();
        $db->shouldReceive('insert')->with('container_testtype3',
            array(
                'ref1' => 4,
                'ref2' => 5,
            )
        )->once();
        $db->shouldIgnoreMissing();

        $properties = new TestType3();
        $properties->foreign()->ref1->persisted(4, self::container($db));
        $properties->foreign()->ref2->persisted(5, self::container($db));

        self::container($db)->saveProperties($properties);
    }

    public function testExceptionOnLoadingWhenPropertiesAreNotFound()
    {
        $this->expectException('Magomogo\\Persisted\\Exception\\NotFound');

        $container = self::container(m::mock(array('fetchAssociative' => false)));

        $notFoundProperties = new TestType1();
        $notFoundProperties->persisted(11, $container);

        $container->loadProperties($notFoundProperties);
    }

    public function testCanDeleteProperties()
    {
        $db = m::mock();
        $db->shouldReceive('delete')->with('container_testtype1', array('id' => 3))->once();
        $db->shouldReceive('delete')->with('container_testtype2', array('id' => 45))->once();

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

//----------------------------------------------------------------------------------------------------------------------

    private static function container($db = null)
    {
        return new Db($db ?: m::mock(array('fetchAssociative' => array())), 'Magomogo\\Persisted\\');
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

