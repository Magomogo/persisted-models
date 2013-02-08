<?php
namespace Keymarker;

use Magomogo\Model\ContainerReadyAbstract;
use Magomogo\Model\PropertyBag;

class Model extends ContainerReadyAbstract
{
    /**
     * @param $id
     * @param null $valuesToSet
     * @return \Magomogo\Model\PropertyBag
     */
    public static function propertiesSample($id = null, $valuesToSet = null)
    {
        return new Properties($id, $valuesToSet);
    }

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function __toString()
    {
        return $this->properties->id;
    }
}
