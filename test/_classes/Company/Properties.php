<?php
namespace Company;
use Magomogo\Model\PropertyBag;

/**
 * @property string $type
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
