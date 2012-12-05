<?php
namespace CreditCard;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testDatatransTestingCreditCard()
    {
        $properties = new Properties;
        $properties->load(new \Model\ContainerArray(
            array(
                'pan' => '9500000000000001',
                'validMonth' => '12',
                'validYear' => '2015',
                'ccv' => '234',
                'cardholderName' => 'John Doe'
            )
        ));

        $cc = new ModelTest($properties);
    }
}
