<?php
namespace Test\JobRecord;

use Magomogo\Persisted\PossessionInterface;
use Magomogo\Persisted\PropertyBag;
use Test\Company;

/**
 * @property string $id
 */
class Properties extends PropertyBag implements PossessionInterface
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
     * @param PropertyBag $properties
     * @param null|string $relationName
     * @return mixed
     */
    public function isOwnedBy($properties, $relationName = null)
    {
        $this->foreign()->$relationName = $properties;
        return $this;
    }
}
