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
}
