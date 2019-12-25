<?php
namespace Magomogo\Persisted\Container;

use Mockery as m;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Test\Person;
use Magomogo\Persisted\Test\Affiliate\Cookie;
use Hamcrest\Matchers;

class SqlDbTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsContainerInterface()
    {
        $this->assertInstanceOf('Magomogo\\Persisted\\Container\\ContainerInterface', self::container());
    }

    public function testFollowsTableNamingConvention()
    {
        $db = self::dbMock();
        $db->shouldReceive('insert')->with('person', Matchers::typeOf('array'), array())->once();
        $db->shouldReceive('insert')->with('creditcard', Matchers::typeOf('array'), array())->once();
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
            ),
            array()
        )->once();

        $properties = new TestType3();
        $properties->foreign()->ref1->persisted(4, self::container($db));
        $properties->foreign()->ref2->persisted(5, self::container($db));

        self::container($db)->saveProperties($properties);
    }

    public function testInsertContainsBooleanProvidePDOTypes()
    {
        $db = self::dbMock(true);
        $db->shouldReceive('insert')->with(
            '`magomogo_persisted_test_affiliate_cookie_properties`',
            array('`id`' => null, '`lifeTime`' => 0, '`isMaster`' => true),
            array('`isMaster`' => \PDO::PARAM_BOOL)
        )->once();
        $properties = new Cookie\Properties(array('isMaster' => true));
        $properties->putIn(self::container($db));
    }

    public function testUpdateContainsBooleanProvidePDOTypes()
    {
        $db = self::dbMock(true);
        $db->shouldReceive('update')->with(
            '`magomogo_persisted_test_affiliate_cookie_properties`',
            array('id' => 5, '`id`' => 5, '`lifeTime`' => 0, '`isMaster`' => true),
            array('id' => 5),
            array('`isMaster`' => \PDO::PARAM_BOOL)
        )->once();
        $properties = new Cookie\Properties(array('id' => 5, 'isMaster' => true));
        $properties->persisted(5, self::container($db));
        $properties->putIn(self::container($db));
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

        $persistedProperties = new TestType1();
        $persistedProperties->persisted(3, self::container($db));

        self::container($db)->deleteProperties($persistedProperties);
    }

    private static function dbMock($wrap = false)
    {
        $db = m::mock();
        $db->shouldReceive('quoteIdentifier')->andReturnUsing(
            function($arg) use ($wrap) {return $wrap ? "`{$arg}`" : $arg;}
        );
        $db->shouldIgnoreMissing();
        return $db;
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function container($db = null)
    {
        return new SqlDb($db ?: m::mock(array('fetchAssoc' => array())), new DbNames());
    }
}


class TestType1 extends AbstractProperties
{
    protected function properties()
    {
        return array();
    }
}

class TestType2 extends AbstractProperties
{
    protected function properties()
    {
        return array();
    }
}

class TestType3 extends AbstractProperties
{
    public function init()
    {
        $this->ownedBy(new TestType1(null), 'ref1');
        $this->ownedBy(new TestType2(null), 'ref2');
    }

    protected function properties()
    {
        return array();
    }
}

