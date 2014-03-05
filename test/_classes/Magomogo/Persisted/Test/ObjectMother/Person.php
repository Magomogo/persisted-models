<?php
namespace Magomogo\Persisted\Test\ObjectMother;

use Magomogo\Persisted\Test\Person\Properties;
use Magomogo\Persisted\Test\Person\Model;

class Person
{
    public static function maxim()
    {
        return new Model(self::maximProperties());
    }

    public static function maximWithoutCC()
    {
        $properties = self::maximProperties();
        $properties->creditCard = null;
        return new Model($properties);
    }

    /**
     * @return Properties
     */
    public static function maximProperties()
    {
        return new Properties(array(
            'title' => 'Mr.',
            'firstName' => 'Maxim',
            'lastName' => 'Gnatenko',
            'email' => 'maxim@xiag.ch',
            'phone' => '+7923-117-2801',
            'creditCard' => CreditCard::datatransTestingProperties(),
            'birthDay' => '1975-07-07T00:00:00+07:00'
        ));
    }
}
