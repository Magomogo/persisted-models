<?php
namespace Person;
use Model\PropertyBag;
use Model\DataType\Text;

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
            'title' => new Text(),
            'firstName' => new Text(),
            'lastName' => new Text(),
            'phone' => new Text(),
            'email' => new Text(),
            'creditCard' => new Text(),
        );
    }

    public function __construct()
    {
        parent::__construct(self::properties());
    }
}
