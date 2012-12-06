<?php
namespace Employee;
use Test\ObjectMother\Company;
use Test\ObjectMother\Person;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $employee = new Model(Company::xiag(), Person::maximProperties());
        $this->assertEquals('Mr. Maxim Gnatenko from XIAG', $employee->greeting());
    }

}
