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
    private $ownerCompanyProperties;

    public function init()
    {
        $this->ownerCompanyProperties = new Company\Properties;
    }

    public function foreign()
    {
        $foreign = new \stdClass();
        $foreign->company = $this->ownerCompanyProperties;
        return $foreign;
    }

    public function isOwnedBy($companyProperties, $relationName = null)
    {
        $this->ownerCompanyProperties = $companyProperties;
        return $this;
    }
}
