<?php
namespace Test\Employee;

use Test\ObjectMother\Employee as EmployeeMother;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $this->assertEquals('Mr. Maxim Gnatenko from XIAG', EmployeeMother::maxim()->greeting());
    }

}
