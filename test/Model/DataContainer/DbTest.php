<?php
namespace Model\DataContainer;
use Mockery as m;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsContainerInterface()
    {
        $this->assertInstanceOf('Model\\DataContainer\\ContainerInterface', self::container());
    }

    private static function container()
    {
        return new Db('Person\\Model', m::mock());
    }
}
