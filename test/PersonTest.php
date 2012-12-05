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

//----------------------------------------------------------------------------------------------------------------------

    private static function person()
    {
        return new Person(
            new Person\Properties(
                array(
                    'title' => 'Mr.',
                    'first_name' => 'John',
                    'last_name' => 'Doe'
                )
            )
        );
    }
}
