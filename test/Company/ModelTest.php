<?php
namespace Test\Company;

use Test\ObjectMother\Company as CompanyMother;
use Mockery as m;

class CompanyTest extends \PHPUnit_Framework_TestCase
{
    public function testAnInstance()
    {
        new Model(new Properties);
    }

    public function testProperties()
    {
        $this->assertEquals('XIAG', CompanyMother::xiag()->name());
    }
}
