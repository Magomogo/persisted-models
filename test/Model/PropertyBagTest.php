<?php
namespace Magomogo\Model;

use Mockery as m;
use Magomogo\Model\PropertyContainer\Db;
use Magomogo\Model\PropertyContainer\Memory;
use Company\Properties as CompanyProperties;

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
        $this->assertNull(self::bag()->id);
    }

    public function testPersistedMessageSetsId()
    {
        $bag = self::bag();
        $bag->persisted('888', m::mock());
        $this->assertEquals('888', $bag->id);
    }

    public function testKnowsItsOrigin()
    {
        $properties = self::bag(11);
        $container = new Db(m::mock(array('fetchAssoc' => array('title' => 'hehe', 'company' => 1))));
        $container->loadProperties($properties);

        $this->assertSame($properties, $properties->assertOriginIs($container));
    }

    public function testRejectsNotConfiguredProperties()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');
        self::bag()->not_configured = 12;
    }

    public function testAssumedThatPropertiesArePersistedInMemory()
    {
        $this->assertTrue(self::bag()->isPersistedIn(new Memory));
        self::bag()->assertOriginIs(new Memory());
    }

    public function testReferencesCanBeExposed()
    {
        $this->assertInstanceOf('Magomogo\\Model\\PropertyBag', self::bag()->foreign()->company);
    }

    public function testAllowsDefineAPropertyHavingNullDefaultValue()
    {
        $properties = self::bag();
        $this->assertNull(self::bag()->nullDefault);
        $properties->nullDefault = 1;
        $this->assertEquals(1, $properties->nullDefault);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function bag($id = null)
    {
        return new TestProperties($id);
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