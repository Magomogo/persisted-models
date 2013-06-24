<?php
namespace Test\Keymarker;

use Magomogo\Persisted\PropertyBag;

/**
 * @property string $id
 * @property \DateTime $created
 */
class Properties extends PropertyBag
{
    protected function properties()
    {
        return array(
            'created' => new \DateTime
        );
    }

    public function constructModel()
    {
        return new Model($this);
    }
}
