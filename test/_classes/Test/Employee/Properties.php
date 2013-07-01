<?php
namespace Test\Employee;

use Test\Keymarker;
use Test\Person;
use Test\Company;

/**
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property \Test\CreditCard\Model $creditCard
 */
class Properties extends Person\Properties
{
    protected function foreigners()
    {
        return array(
            'company' => new Company\Properties
        );
    }

    public function putIn($container, $companyProperties)
    {
        $this->foreign()->company = $companyProperties;
        return parent::putIn($container);
    }
}
