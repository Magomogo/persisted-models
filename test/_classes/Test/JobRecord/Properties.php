<?php
namespace Test\JobRecord;

use Magomogo\Persisted\PropertyBag;
use Test\Company;

/**
 * @property string $id
 */
class Properties extends PropertyBag
{
    protected function properties()
    {
        return array();
    }

    protected function foreigners()
    {
        return array(
            'currentCompany' => new Company\Properties,
            'previousCompany' => new Company\Properties
        );
    }

    /**
     * @return self
     */
    public function constructModel()
    {
        return new Model(
            $this->foreign()->currentCompany->constructModel(),
            $this->foreign()->previousCompany->constructModel(),
            $this
        );
    }

}
