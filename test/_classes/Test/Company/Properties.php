<?php
namespace Test\Company;

use Magomogo\Persisted\PropertyBag;

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
