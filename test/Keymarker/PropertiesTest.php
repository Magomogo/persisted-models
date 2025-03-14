<?php
namespace Test\Keymarker;
use PHPUnit\Framework\TestCase;

class PropertiesTest extends TestCase
{
    public function testHasNaturalId()
    {
        $properties = new Properties(array('id' => 'Natural'));
        $this->assertEquals('Natural', $properties->naturalKey());
    }
}
