<?php
namespace Magomogo\Persisted;

use Mockery as m;
use Magomogo\Persisted\Container\SqlDb;
use Magomogo\Persisted\Container\Memory;
use Magomogo\Persisted\Test\DbNames;
use Magomogo\Persisted\Test\ObjectMother;
use Magomogo\Persisted\Test\Person;

class AbstractPropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testIsIterable()
    {
        $names = array();
        $properties = array();
        foreach (self::properties() as $name => $property) {
            $names[] = $name;
            $properties[] = $property;
        }

        $this->assertEquals(array('title', 'description', 'object', 'nullDefault'), $names);
        $this->assertEquals(array('default title', 'default descr', new \stdClass(), null), $properties);
    }

    public function testIdIsNullInitially()
    {
        $this->assertNull(self::properties()->id(m::mock()));
    }

    public function testPersistedMessageSetsId()
    {
        $props = self::properties();
        $props->persisted('888', m::mock());
        $this->assertEquals('888', $props->id(m::mock()));
    }

    public function testPropertiesCanHaveDifferentIdsInDifferentContainers()
    {
        $props = self::properties();
        $container1 = new SqlDb(m::mock(), new DbNames());
        $container2 = new Memory();

        $props->persisted('888', $container1);
        $props->persisted('2342-klsjdf94', $container2);

        $this->assertEquals('888', $props->id($container1));
        $this->assertEquals('2342-klsjdf94', $props->id($container2));
    }

    public function testRejectsNotConfiguredProperties()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');
        self::properties()->not_configured = 12;
    }

    public function testAllowsDefineAPropertyHavingNullDefaultValue()
    {
        $properties = self::properties();
        $this->assertNull(self::properties()->nullDefault);
        $properties->nullDefault = 1;
        $this->assertEquals(1, $properties->nullDefault);
    }

    public function testCloneCreatesEqualProperties()
    {
        $p = self::properties();
        $this->assertEquals($p, clone $p);
    }

    public function testPreventsFromCreatingArbitraryProperties()
    {
        $p = self::properties();

        $this->setExpectedException('PHPUnit_Framework_Error_Notice');
        $p->anyName = 'hehe';
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function properties()
    {
        return new TestProperties();
    }

}

//======================================================================================================================

class TestProperties extends AbstractProperties
{
    public $title = 'default title';
    public $description = 'default descr';
    public $object;
    public $nullDefault;

    protected function init()
    {
        $this->object = new \stdClass();
    }
}