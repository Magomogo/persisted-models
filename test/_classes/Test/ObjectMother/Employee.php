<?php
namespace Test\ObjectMother;

use Employee\Properties;
use Employee\Model;
use Company\Model as CompanyModel;
use Company\Properties as CompanyProperties;

class Employee
{
    public static function maxim($id = null)
    {
        $maxim = self::maximProperties($id);
        return new Model(new CompanyModel($maxim->reference('company')), $maxim);
    }

    /**
     * @param null $id
     * @return Properties
     */
    public static function maximProperties($id = null)
    {
        $properties = new Properties(
            $id,
            array(
                'company' => new CompanyProperties(null, array(), array('name' => 'XIAG'))
            ),
            array(
                'title' => 'Mr.',
                'firstName' => 'Maxim',
                'lastName' => 'Gnatenko',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => CreditCard::datatransTesting($id),
                'birthDay' => new \DateTime('1975-07-07T00:00:00+07:00')
            )
        );
        return $properties;
    }
}
