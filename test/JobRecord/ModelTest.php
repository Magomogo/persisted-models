<?php

namespace Test\JobRecord;

use Test\ObjectMother\Company;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
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
