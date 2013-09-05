<?php
namespace Magomogo\Persisted\Test\ObjectMother;

use Magomogo\Persisted\Test\Employee\Properties as EmployeeProperties;
use Magomogo\Persisted\Test\Employee\Model as EmployeeModel;
use Magomogo\Persisted\Test\Company\Model as CompanyModel;

class Employee
{
    public static function maxim()
    {
        $maxim = self::maximProperties();
        return new EmployeeModel(new CompanyModel($maxim->foreign()->company), $maxim, $maxim->tags);
    }

    /**
     * @return EmployeeProperties
     */
    public static function maximProperties()
    {
        $properties = new EmployeeProperties(
            array(
                'title' => 'Mr.',
                'firstName' => 'Maxim',
                'lastName' => 'Gnatenko',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => CreditCard::datatransTesting(),
                'birthDay' => new \DateTime('1975-07-07T00:00:00+07:00')
            )
        );
        $properties->foreign()->company->name = 'XIAG';

        return $properties;
    }
}
