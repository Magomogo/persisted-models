<?php

namespace Magomogo\Persisted\Test\JobRecord;

use Magomogo\Persisted\Test\ObjectMother\Company;

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
