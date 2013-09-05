<?php
namespace Magomogo\Persisted\Test\Company;

use Magomogo\Persisted\PropertyBag;

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
