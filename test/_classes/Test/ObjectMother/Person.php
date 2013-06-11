<?php
namespace Test\ObjectMother;

use Person\Properties;
use Person\Model;

class Person
{
    public static function maxim($id = null)
    {
        return new Model(self::maximProperties($id));
    }

    public static function maximWithoutCC()
    {
        $properties = self::maximProperties();
        $properties->creditCard = null;
        return new Model($properties);
    }

    /**
     * @param null $id
     * @return Properties
     */
    public static function maximProperties($id = null)
    {
        return new Properties($id, array(
            'title' => 'Mr.',
            'firstName' => 'Maxim',
            'lastName' => 'Gnatenko',
            'email' => 'maxim@xiag.ch',
            'phone' => '+7923-117-2801',
            'creditCard' => CreditCard::datatransTesting($id),
            'birthDay' => new \DateTime('1975-07-07T00:00:00+07:00')
        ));
    }
}
