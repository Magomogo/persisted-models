<?php
namespace Test\Person;

use Magomogo\Persisted\PropertyBag;
use Test\CreditCard\Model as CreditCard;
use Test\CreditCard\Properties as CreditCardProperties;

/**
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property \Test\CreditCard\Model $creditCard
 */
class Properties extends PropertyBag
{
    protected function properties()
    {
        return array(
            'title' => '',
            'firstName' => '',
            'lastName' => '',
            'phone' => '',
            'email' => '',
            'creditCard' => new CreditCard(new CreditCardProperties),
            'birthDay' => new \DateTime('1970-01-01')
        );
    }
}
