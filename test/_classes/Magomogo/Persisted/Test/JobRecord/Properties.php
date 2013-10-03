<?php
namespace Magomogo\Persisted\Test\JobRecord;

use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Test\Company;

class Properties extends AbstractProperties
{
    protected function init()
    {
        $this->ownedBy(new Company\Properties(), 'currentCompany');
        $this->ownedBy(new Company\Properties(), 'previousCompany');
    }
}
