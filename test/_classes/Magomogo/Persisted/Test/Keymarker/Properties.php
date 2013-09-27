<?php
namespace Magomogo\Persisted\Test\Keymarker;

use Magomogo\Persisted\AbstractProperties;

/**
 * @property string $name
 * @property \DateTime $created
 */
class Properties extends AbstractProperties
{
    protected function properties()
    {
        return array(
            'name' => '',
            'created' => new \DateTime(date('c'))
        );
    }

    public function naturalKeyFieldName()
    {
        return 'name';
    }
}
