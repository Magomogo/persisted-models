<?php
namespace Test\Employee;

use Test\Person\Properties as PersonProperties;
use Test\Company\Properties as CompanyProperties;

/**
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property \Test\CreditCard\Model $creditCard
 */
class Properties extends PersonProperties
{
    protected function foreigners()
    {
        return array(
            'company' => new CompanyProperties
        );
    }
}
