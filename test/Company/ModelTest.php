<?php
namespace Test\Company;

use Test\ObjectMother\Company as CompanyMother;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class CompanyTest extends TestCase
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
