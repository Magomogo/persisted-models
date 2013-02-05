<?php
namespace Test\ObjectMother;

use CreditCard\Properties;
use CreditCard\Model;

class CreditCard
{
    public static function datatransTesting($id = null)
    {
        return new Model(new Properties($id, array(
            'system' => 'VISA',
            'pan' => '9500000000000001',
            'validMonth' => '12',
            'validYear' => '2015',
            'ccv' => '234',
            'cardholderName' => 'Maxim Gnatenko'
        )));
    }

}
