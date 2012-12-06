<?php
namespace Model\DataContainer;
use Mockery as m;
use Test\ObjectMother\Person;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsContainerInterface()
    {
        $this->assertInstanceOf('Model\\DataContainer\\ContainerInterface', self::container());
    }

    public function testFollowsTableNamingConvention()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('person_model', typeOf('array'))->once();
        $db->shouldReceive('insert')->with('creditcard_model', typeOf('array'))->once();
        $db->shouldIgnoreMissing();
        Person::maxim()->putIn(self::container($db));
    }

    private static function container($db = null)
    {
        return new Db('Person\\Model', $db ?: m::mock());
    }
}
