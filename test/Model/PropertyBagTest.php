<?php
namespace Model;
use Model\DataType\Text;

class PropertyBagTest extends \PHPUnit_Framework_TestCase
{
    public function testIsIterable()
    {
        $bag = new PropertyBag(array(
            'title' => new Text('default title'),
            'description' => new Text('default descr'),
        ));

        $names = array();
        $properties = array();
        foreach ($bag as $name => $property) {
            $names[] = $name;
            $properties[] = $property;
        }

        $this->assertEquals(array('title', 'description'), $names);
        $this->assertEquals(array(new Text('default title'), new Text('default descr')), $properties);

    }

}
