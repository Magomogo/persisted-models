<?php
namespace Person;
use Magomogo\Model\PropertyBag;

/**
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property \CreditCard\Model $creditCard
 */
class Properties extends PropertyBag
{
    protected static function properties()
    {
        return array(
            'title' => '',
            'firstName' => '',
            'lastName' => '',
            'phone' => '',
            'email' => '',
            'creditCard' => new \CreditCard\Model(new \CreditCard\Properties()),
            'birthDay' => new \DateTime('1970-01-01')
        );
    }
}
