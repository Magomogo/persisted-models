<?php
namespace Company;
use \Test\ObjectMother\Company;
use Mockery as m;

class CompanyTest extends \PHPUnit_Framework_TestCase
{
    public function testAnInstance()
    {
        new Model(new Properties);
    }

    public function testProperties()
    {
        $this->assertEquals('XIAG', Company::xiag()->name());
    }

    public function testGetsEmployeeByItsId()
    {
        $db = m::mock('Doctrine\\DBAL\\Connection', array('executeQuery' => m::mock(array('fetchAll' => array(1)))));
        $db->shouldReceive('fetchAssoc')->andReturn(array());

        $this->assertInstanceOf(
            'Employee\\Model',
            Company::xiag()->getEmployeeById(34, $db)
        );
    }
}
