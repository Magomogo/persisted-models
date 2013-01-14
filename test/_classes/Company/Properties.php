<?php
namespace Company;
use Magomogo\Model\PropertyBag;

/**
 * @property string $name
 */
class Properties extends PropertyBag
{
    protected static function properties()
    {
        return array(
            'name' => '',
        );
    }
}
