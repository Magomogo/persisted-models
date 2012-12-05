<?php
namespace Person;
use Person\DataSource\Db;
use Mockery as m;

class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeLoadedFromDatabase()
    {
        $properties = new Properties();
        $properties->load(self::dbDataSource());

        $this->assertEquals('John', $properties->firstName);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function dbDataSource()
    {
        $stm = m::mock(array('fetch' => array(
            'title' => 'Mr.',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'maxim@xiag.ch',
            'phone' => '+7923-117-2801',
        )));
        $stm->shouldIgnoreMissing();
        return new Db(88, m::mock(array('prepare' => $stm)));
    }
}
