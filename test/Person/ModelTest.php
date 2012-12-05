<?php
namespace Person;
use Person\DataSource\Form;

class ModelTest extends \PHPUnit_Framework_TestCase
{
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

//----------------------------------------------------------------------------------------------------------------------

    private static function person()
    {
        $properties = new Properties();
        $properties->load(
            new Form(
                array(
                    'title' => 'Mr.',
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'maxim@xiag.ch',
                    'phone' => '+7923-117-2801',
                )
            )
        );
        return new Model($properties);
    }
}
