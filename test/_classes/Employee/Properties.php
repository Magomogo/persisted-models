<?php
namespace Employee;
use Person\Properties as PersonProperties;
use Company\Properties as CompanyProperties;

/**
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property \CreditCard\Model $creditCard
 */
class Properties extends PersonProperties
{
    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id)
    {
        $properties = new self(
            $id,
            array(
                'company' => new CompanyProperties
            )
        );
        $container->loadProperties($properties);
        return $properties;
    }

}
