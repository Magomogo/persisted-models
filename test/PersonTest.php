<?php

class PersonTest extends PHPUnit_Framework_TestCase
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
        return new Person(
            new Person\Properties(
                array(
                    'title' => 'Mr.',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'maxim@xiag.ch',
                    'phone' => '+7923-117-2801',
                )
            )
        );
    }
}
