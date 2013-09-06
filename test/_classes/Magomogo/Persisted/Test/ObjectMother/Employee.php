<?php
namespace Magomogo\Persisted\Test\ObjectMother;

use Magomogo\Persisted\Test\Employee\Properties as EmployeeProperties;
use Magomogo\Persisted\Test\Employee\Model as EmployeeModel;

class Employee
{
    public static function maxim($company = null)
    {
        return new EmployeeModel(
            $company ?: Company::xiag(),
            new EmployeeProperties(
                array(
                    'title' => 'Mr.',
                    'firstName' => 'Maxim',
                    'lastName' => 'Gnatenko',
                    'email' => 'maxim@xiag.ch',
                    'phone' => '+7923-117-2801',
                    'creditCard' => CreditCard::datatransTesting(),
                    'birthDay' => new \DateTime('1975-07-07T00:00:00+07:00')
                )
            )
        );
    }
}
