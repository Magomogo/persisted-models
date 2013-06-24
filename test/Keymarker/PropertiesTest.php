<?php
namespace Test\Keymarker;

class PrepertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testHasNaturalId()
    {
        $properties = new Properties(array('id' => 'Natural'));
        $this->assertEquals('Natural', $properties->naturalKey());
    }
}
