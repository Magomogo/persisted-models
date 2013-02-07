<?php
namespace Company;
use Test\ObjectMother\Company as TestCompany;
use Mockery as m;

class CompanyTest extends \PHPUnit_Framework_TestCase
{
    public function testAnInstance()
    {
        new Model(Model::propertiesSample());
    }

    public function testProperties()
    {
        $this->assertEquals('XIAG', TestCompany::xiag()->name());
    }
}
