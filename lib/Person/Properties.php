<?php
namespace Person;
use Model\PropertyBag;

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
    private static function properties()
    {
        return array(
            'title' => '',
            'firstName' => '',
            'lastName' => '',
            'phone' => '',
            'email' => '',
            'creditCard' => new \CreditCard\Model(new \CreditCard\Properties()),
        );
    }

    public function __construct($id = null)
    {
        parent::__construct(self::properties(), $id);
    }
}
