<?php

class Person
{
    /**
     * @var Person\Properties
     */
    private $properties;

    public function __construct(Person\Properties $properties)
    {
        $this->properties = $properties;
    }

    public function politeTitle()
    {
        return $this->properties->title . ' ' . $this->properties->firstName . ' ' . $this->properties->lastName;
    }
}
