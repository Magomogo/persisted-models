<?php
namespace Test\Keymarker;

use Magomogo\Persisted\PropertyBag;

/**
 * @property string $name
 * @property \DateTime $created
 */
class Properties extends PropertyBag
{
    protected function properties()
    {
        return array(
            'id' => '',
            'created' => new \DateTime
        );
    }

    public function naturalKey()
    {
        return $this->id;
    }
}
