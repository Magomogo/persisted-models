<?php
namespace Test\Person;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Test\ObjectMother;

class ModelTest extends TestCase
{

    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testCreatingANewPerson()
    {
        $person = new Model(new Properties);
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

    public function testCanBeSavedIntoAPropertyContainer()
    {
        $container = m::mock();
        $container->shouldReceive('saveProperties')
            ->with(m::on(function($p) use ($container) {$p->persisted(15, $container); return true;}))
            ->once();
        $container->shouldIgnoreMissing();

        $id = self::person()->save($container);
        $this->assertEquals(15, $id);
    }

    public function testCanBeTaggedWithAKeymarker()
    {
        $person = self::person();
        $person->tag(ObjectMother\Keymarker::friend());
        $person->tag(ObjectMother\Keymarker::IT());

        $this->assertEquals('Friend, IT', $person->taggedAs());
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function person()
    {
        return new Model(self::johnDoeProperties());
    }

    private static function johnDoeProperties()
    {
        return new Properties(
            array(
                'title' => 'Mr.',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => ObjectMother\CreditCard::datatransTesting()
            )
        );
    }

    private static function personWithoutCreditCard()
    {
        return new Model(new Properties(
            array(
                'title' => 'Mr.',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'maxim@xiag.ch',
                'phone' => '+7923-117-2801',
                'creditCard' => null
            )
        ));
    }
}
