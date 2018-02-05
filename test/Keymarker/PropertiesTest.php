<?php
namespace Magomogo\Persisted\Test\Keymarker;

use Magomogo\Persisted\Container\Memory;

class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testHasNaturalId()
    {
        $properties = new Properties(array('name' => 'Natural'));
        $container = new Memory();
        $container->saveProperties($properties);
        $this->assertEquals('Magomogo\Persisted\Test\Keymarker\Properties_Natural', $properties->id($container));
    }
}
