<?php
namespace Test\Company;

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

    /**
     * @return Model
     */
    public function constructModel()
    {
        return new Model($this);
    }

}
