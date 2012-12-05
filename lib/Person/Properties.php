<?php
namespace Person;

class Properties
{

    public $title;
    public $firstName;
    public $lastName;

    public function __construct($dbRow)
    {
        $this->title = array_key_exists('title', $dbRow) ? $dbRow['title'] : null;
        $this->firstName = array_key_exists('first_name', $dbRow) ? $dbRow['first_name'] : null;
        $this->lastName = array_key_exists('last_name', $dbRow) ? $dbRow['last_name'] : null;
    }
}
