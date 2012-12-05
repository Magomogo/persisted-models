<?php
namespace CreditCard;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testDatatransTestingCreditCard()
    {
        $this->assertEquals(
            '9500 **** **** 0001',
            self::cc()->maskedPan()
        );
    }

    private static function cc()
    {
        $properties = new Properties;
        $properties->load(
            new \Model\ContainerArray(
                array(
                    'system' => 'VISA',
                    'pan' => '9500000000000001',
                    'validMonth' => '12',
                    'validYear' => '2015',
                    'ccv' => '234',
                    'cardholderName' => 'John Doe'
                )
            )
        );

        return new Model($properties);
    }
}
