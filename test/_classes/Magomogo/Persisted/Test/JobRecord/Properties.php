<?php
namespace Magomogo\Persisted\Test\JobRecord;

use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Test\Company;

/**
 * @property string $id
 */
class Properties extends AbstractProperties
{
    protected function properties()
    {
        return array();
    }

    protected function init()
    {
        $this->ownedBy(new Company\Properties(), 'currentCompany');
        $this->ownedBy(new Company\Properties(), 'previousCompany');
    }
}
