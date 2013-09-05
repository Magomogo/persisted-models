<?php
namespace Magomogo\Persisted\Test\Keymarker;

use Magomogo\Persisted\Test\ObjectMother\Keymarker;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeRepresentedAsAString()
    {
        $this->assertEquals('Friend', strval(Keymarker::friend()));
    }
}
