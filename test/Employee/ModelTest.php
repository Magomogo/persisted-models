<?php
namespace Test\Employee;

use Test\ObjectMother\Employee as EmployeeMother;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testInstantiate()
    {
        $this->assertEquals('Mr. Maxim Gnatenko from XIAG', EmployeeMother::maxim()->greeting());
    }

}
