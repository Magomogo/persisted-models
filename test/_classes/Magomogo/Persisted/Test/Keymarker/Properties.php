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
            'id' => '',
            'created' => new \DateTime(date('c'))
        );
    }

    public function naturalKey()
    {
        return $this->id;
    }
}
