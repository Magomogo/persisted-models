<?php
namespace Employee;
use Test\ObjectMother\Employee;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $this->assertEquals('Mr. Maxim Gnatenko from XIAG', Employee::maxim()->greeting());
    }

}
