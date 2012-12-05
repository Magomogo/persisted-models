<?php
namespace Person;
use Model\ContainerArray;

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

//----------------------------------------------------------------------------------------------------------------------

    private static function person()
    {
        $properties = new Properties();
        $properties->load(
            new ContainerArray(
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
