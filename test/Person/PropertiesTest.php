<?php
namespace Person;

class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeLoadedFromDatabase()
    {
        $properties = new Properties(
            array(
                'first_name' => 'John'
            )
        );

        $this->assertEquals('John', $properties->firstName);
    }
}
