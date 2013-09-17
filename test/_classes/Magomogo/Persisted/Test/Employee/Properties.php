<?php
namespace Magomogo\Persisted\Test\Employee;

use Magomogo\Persisted\PossessionInterface;
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

    public function ownedBy($companyProperties, $relationName = null)
    {
        $this->ownerCompanyProperties = $companyProperties;
        return $this;
    }
}
