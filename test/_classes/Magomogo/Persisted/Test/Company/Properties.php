<?php
namespace Magomogo\Persisted\Test\Company;

use Magomogo\Persisted\AbstractProperties;

/**
 * @property string $name
 */
class Properties extends AbstractProperties
{
    protected function properties()
    {
        return array(
            'name' => '',
        );
    }
}
