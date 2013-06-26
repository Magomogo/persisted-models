<?php
namespace Magomogo\Persisted;

use Mockery as m;
use Magomogo\Persisted\Container\Db;
use Magomogo\Persisted\Container\Memory;
use Test\Company\Properties as CompanyProperties;
use Test\ObjectMother\Employee;
use Test\Employee\Properties as EmployeeProperties;

class PropertyBagTest extends \PHPUnit_Framework_TestCase
{
    public function testIsIterable()
    {
        $names = array();
        $properties = array();
        foreach (self::bag() as $name => $property) {
            $names[] = $name;
            $properties[] = $property;
        }

        $this->assertEquals(array('title', 'description', 'object', 'nullDefault'), $names);
        $this->assertEquals(array('default title', 'default descr', new \stdClass(), null), $properties);
    }

    public function testIdIsNullInitially()
    {
        $this->assertNull(self::bag()->id(m::mock()));
    }

    public function testPersistedMessageSetsId()
    {
        $bag = self::bag();
        $bag->persisted('888', m::mock());
        $this->assertEquals('888', $bag->id(m::mock()));
    }

    public function testPropertiesBagCanHaveDifferentIdsInDifferentContainers()
    {
        $bag = self::bag();
        $container1 = new Db(m::mock());
        $container2 = new Memory();

        $bag->persisted('888', $container1);
        $bag->persisted('2342-klsjdf94', $container2);

        $this->assertEquals('888', $bag->id($container1));
        $this->assertEquals('2342-klsjdf94', $bag->id($container2));
    }

    public function testRejectsNotConfiguredProperties()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');
        self::bag()->not_configured = 12;
    }

    public function testReferencesCanBeExposed()
    {
        $this->assertInstanceOf('Magomogo\\Persisted\\PropertyBag', self::bag()->foreign()->company);
    }

    public function testAllowsDefineAPropertyHavingNullDefaultValue()
    {
        $properties = self::bag();
        $this->assertNull(self::bag()->nullDefault);
        $properties->nullDefault = 1;
        $this->assertEquals(1, $properties->nullDefault);
    }

    public function testPropertiesCanBeCopiedToAnotherBag()
    {
        $properties = Employee::maximProperties();
        $properties->persisted('1', m::mock('Magomogo\\Persisted\\Container\\Db'));
        $properties->persisted('123123132', m::mock('Magomogo\\Persisted\\Container\\Memory'));

        $anotherProperties = new EmployeeProperties();
        $properties->copyTo($anotherProperties);

        $this->assertNotSame($properties, $anotherProperties);
        $this->assertEquals($properties, $anotherProperties);
    }

    public function testCloneCreatesEqualBag()
    {
        $bag = self::bag();
        $this->assertEquals($bag, clone $bag);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function bag()
    {
        return new TestProperties();
    }

}

class TestProperties extends PropertyBag
{

    protected function properties()
    {
        return array(
            'title' => 'default title',
            'description' => 'default descr',
            'object' => new \stdClass(),
            'nullDefault' => null
        );
    }

    protected function foreigners()
    {
        return array(
            'company' => new CompanyProperties
        );
    }
}