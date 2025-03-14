<?php
namespace Test\Keymarker;

use Test\ObjectMother\Keymarker;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testCanBeRepresentedAsAString()
    {
        $this->assertEquals('Friend', strval(Keymarker::friend()));
    }
}
