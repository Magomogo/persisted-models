<?php
namespace Test\ObjectMother;

use Model\DataContainer\ArrayMap;
use Person\Properties;
use Person\Model;

class Person
{
    public static function maxim($id = null)
    {
        $container = new ArrayMap(array(
            'title' => 'Mr.',
            'firstName' => 'Maxim',
            'lastName' => 'Gnatenko',
            'email' => 'maxim@xiag.ch',
            'phone' => '+7923-117-2801',
            'creditCard' => CreditCard::datatransTesting($id)
        ));

        return new Model($container->loadProperties(new Properties($id)));
    }

}
