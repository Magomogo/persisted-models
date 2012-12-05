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
    private $properties;

    public function __construct()
    {
        $this->properties = self::properties();
    }

    public function load(\Model\ContainerInterface $container)
    {
        $container->loadProperties($this->properties);
        return $this;
    }

    public function save(\Model\ContainerInterface $container)
    {
        $container->saveProperties($this->properties);
        return $this;
    }

    public function __get($name)
    {
        return $this->prop($name)->value();
    }

    public function __set($name, $value)
    {
        $this->prop($name)->setValue($value);
    }

//----------------------------------------------------------------------------------------------------------------------

    /**
     * @param $name
     * @return \Model\DataType\DataTypeInterface
     */
    private function prop($name)
    {
        return $this->properties[$name];
    }

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
