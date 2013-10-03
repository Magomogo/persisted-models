<?php
namespace Magomogo\Persisted\Test\Employee;

use Magomogo\Persisted\Test\Keymarker;
use Magomogo\Persisted\Test\Person;
use Magomogo\Persisted\Test\Company;

class Properties extends Person\Properties
{
    protected function init()
    {
        parent::init();
        $this->ownedBy(new Company\Properties(), 'company');
    }
}
