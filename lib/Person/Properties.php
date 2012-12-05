<?php
namespace Person;
use Model\DataType\Text;

/**
 * @property $title
 * @property $firstName
 * @property $lastName
 * @property $phone
 * @property $email
 */
class Properties
{
    /**
     * @var string
     */
    private $uniqueKey;

    private $properties;

    public function __construct($uniqueKey = null)
    {
        $this->uniqueKey = $uniqueKey;
        $this->properties = self::properties();
    }

    public function load(\Model\ContainerInterface $container)
    {
        $container->begin($this->uniqueKey);
        foreach ($this->properties as $name => $property) {
            $container->loadProperty($name, $property);
        }
        return $this;
    }

    public function save(\Model\ContainerInterface $container)
    {
        $container->begin($this->uniqueKey);
        foreach ($this->properties as $name => $property) {
            $container->saveProperty($name, $property);
        }
        $this->uniqueKey = $container->commit();
        return $this;
    }

    public function __get($name)
    {
        return $this->properties[$name]->value();
    }

    public function __set($name, $value)
    {
        $this->properties[$name]->setValue($value);
    }

//----------------------------------------------------------------------------------------------------------------------

    private static function properties()
    {
        return array(
            'title' => new Text(),
            'firstName' => new Text(),
            'lastName' => new Text(),
            'phone' => new Text(),
            'email' => new Text(),
        );
    }
}
