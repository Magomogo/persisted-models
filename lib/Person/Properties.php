<?php
namespace Person;

class Properties
{
    public $title;
    public $firstName;
    public $lastName;
    public $phone;
    public $email;

    public function load(\Model\DataSourceInterface $dataSource)
    {
        foreach ($this as $propertyName => $foo) {
            $this->{$propertyName} = $dataSource->readValue($propertyName);
        }
    }
}
