<?php
namespace JobRecord;

use Magomogo\Persisted\PropertyBag;
use Company\Properties as CompanyProperties;

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
            'currentCompany' => new CompanyProperties,
            'previousCompany' => new CompanyProperties
        );
    }
}
