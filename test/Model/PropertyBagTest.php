<?php
namespace Model;
use Model\DataType\Text;

class PropertyBagTest extends \PHPUnit_Framework_TestCase
{
    public function testIsIterable()
    {
        $names = array();
        $properties = array();
        foreach (self::bag() as $name => $property) {
            $names[] = $name;
            $properties[] = $property;
        }

        $this->assertEquals(array('title', 'description'), $names);
        $this->assertEquals(array(new Text('default title'), new Text('default descr')), $properties);

    }

    public function testIdIsNullInitially()
    {
        $this->assertNull(self::bag()->id);
    }

    public function testPersistedMessageSetsId()
    {
        $bag = self::bag();
        $bag->persisted('888');
        $this->assertEquals('888', $bag->id);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function bag()
    {
        $bag = new PropertyBag(array(
            'title' => new Text('default title'),
            'description' => new Text('default descr'),
        ));
        return $bag;
    }

}
