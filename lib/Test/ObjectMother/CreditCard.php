<?php
namespace Test\ObjectMother;

use Model\DataContainer\ArrayMap;
use CreditCard\Properties;
use CreditCard\Model;

class CreditCard
{
    public static function datatransTesting($id = null)
    {
        $container = new ArrayMap(array(
            'system' => 'VISA',
            'pan' => '9500000000000001',
            'validMonth' => '12',
            'validYear' => '2015',
            'ccv' => '234',
            'cardholderName' => 'Maxim Gnatenko'
        ));

        return new Model($container->loadProperties(new Properties($id)));
    }

}
