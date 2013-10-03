<?php
namespace Magomogo\Persisted\Test\Employee;

use Magomogo\Persisted\Test\Keymarker;
use Magomogo\Persisted\Test\Person;
use Magomogo\Persisted\Test\Company;

/**
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property \Magomogo\Persisted\Test\CreditCard\Model $creditCard
 */
class Properties extends Person\Properties
{
    protected function init()
    {
        parent::init();
        $this->ownedBy(new Company\Properties(), 'company');
    }
}
