<?php
namespace Test\Employee;

use Magomogo\Persisted\PossessionInterface;
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
class Properties extends Person\Properties implements PossessionInterface
{
    protected function foreigners()
    {
        return array(
            'company' => new Company\Properties
        );
    }

    public function isOwnedBy($companyProperties, $relationName = null)
    {
        $this->foreign()->company = $companyProperties;
        return $this;
    }
}
