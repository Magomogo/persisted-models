<?php
namespace Person;
use Model\PropertyBag;
use Model\DataType\Text;

/**
 * @property $title
 * @property $firstName
 * @property $lastName
 * @property $phone
 * @property $email
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
        );
    }

    public function __construct()
    {
        parent::__construct(self::properties());
    }
}
