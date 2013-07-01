<?php

namespace Test\JobRecord;

use Test\ObjectMother\Company;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testShowsSomeDescription()
    {
        $this->assertEquals(
            'NSTU -> XIAG',
            self::jobRecord()->description()
        );
    }

    private static function jobRecord()
    {
        return new Model(Company::xiag(), Company::nstu());
    }
}
