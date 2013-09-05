<?php
namespace Magomogo\Persisted\Test\Keymarker;

class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testHasNaturalId()
    {
        $properties = new Properties(array('id' => 'Natural'));
        $this->assertEquals('Natural', $properties->naturalKey());
    }
}
