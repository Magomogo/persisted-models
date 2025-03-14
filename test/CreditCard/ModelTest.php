<?php
namespace Test\CreditCard;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
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
        return new Model(new Properties(array(
            'system' => 'VISA',
            'pan' => '9500000000000001',
            'validMonth' => '12',
            'validYear' => '2015',
            'ccv' => '234',
            'cardholderName' => 'John Doe'
        )));
    }
}
