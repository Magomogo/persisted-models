<?php
namespace Company;
use Magomogo\Model\PropertyBag;

/**
 * @property string $name
 */
class Properties extends PropertyBag
{
    protected function properties()
    {
        return array(
            'name' => '',
        );
    }
}
