<?php
namespace Person;
use Model\DataContainer\ArrayMap;
use Mockery as m;
use Test\ObjectMother\CreditCard;

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

    public function testCanBeSavedIntoADataContainer()
    {
        $container = m::mock('Model\\DataContainer\\ContainerInterface');
        $container->shouldReceive('saveProperties')->once();

        self::person()->putInto($container);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function person()
    {
        return new Model(self::johnDoeProperties());
    }

    private static function johnDoeProperties()
    {
        return self::personProperties(
            array(
                'title' => 'Mr.',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => CreditCard::datatransTesting()
            )
        );
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
        $container = new ArrayMap($map);
        return $container->loadProperties(new Properties());
    }
}
