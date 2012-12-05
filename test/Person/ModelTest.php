<?php
namespace Person;
use Model\DataContainer\ArrayMap;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingANewPerson()
    {
        $person = new Model(new Properties());
    }

    public function testReadItsPropertiesToImplementSomeBusinessLogic()
    {
        $this->assertEquals(
            'Mr. John Doe',
            self::person()->politeTitle()
        );
    }

    public function testStateCanBeChangedByAMessage()
    {
        $person = self::person();
        $person->phoneNumberIsChanged('335-65-66');

        $this->assertContains('Phone: 335-65-66', $person->contactInfo());
    }

    public function testHasAccessToCreditCardModel()
    {
        $this->assertEquals('VISA, 9500 **** **** 0001', self::person()->paymentInfo());
    }

    public function testBehaviorWhenNoCreditCard()
    {
        $this->assertNull(self::personWithoutCreditCard()->paymentInfo());
        $this->assertFalse(self::personWithoutCreditCard()->ableToPay());
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function person()
    {
        return new Model(self::personProperties(
            array(
                'title' => 'Mr.',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => self::cc()
            )
        ));
    }

    private static function personWithoutCreditCard()
    {
        return new Model(self::personProperties(
            array(
                'title' => 'Mr.',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
            )
        ));
    }

    private static function personProperties(array $map)
    {
        $properties = new Properties();
        $properties->load(new ArrayMap($map));
        return $properties;
    }

    private static function cc()
    {
        $properties = new \CreditCard\Properties();
        $properties->load(
            new ArrayMap(
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
        return new \CreditCard\Model($properties);
    }
}
