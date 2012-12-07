<?php
namespace Person;
use Model\DataContainer\ContainerInterface;
use Model\ContainerReadyInterface;
use Company;
use Employee;
use Doctrine\DBAL\Connection;

class Model implements ContainerReadyInterface
{
    /**
     * @var Properties
     */
    private $properties;

    public static function loadFrom(ContainerInterface $container, $id)
    {
        $properties = new Properties($id);
        $container->loadProperties($properties);
        return new self($properties);
    }

    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    public function politeTitle()
    {
        return $this->properties->title . ' ' . $this->properties->firstName . ' ' . $this->properties->lastName;
    }

    public function contactInfo()
    {
        return 'Phone: ' . $this->properties->phone . "\n" . 'Email: ' . $this->properties->email;
    }

    public function phoneNumberIsChanged($newNumber)
    {
        $this->properties->phone = $newNumber;
    }

    public function paymentInfo()
    {
        return $this->ableToPay() ?
            $this->properties->creditCard->paymentSystem() . ', ' . $this->properties->creditCard->maskedPan() : null;
    }

    public function ableToPay()
    {
        return !is_null($this->properties->creditCard);
    }

    /**
     * @param \Company\Model $company
     * @param \Doctrine\DBAL\Connection $db
     * @return \Employee\Model|null
     */
    public function hiredBy(Company\Model $company, Connection $db)
    {
        $count = $db->executeUpdate(
            'UPDATE person_properties SET company_id = :companyId WHERE id = :employeeId',
            array('employeeId' => $this->id(), 'companyId' => $company->id())
        );
        if ($count) {
            return new Employee\Model($company, $this->properties);
        }
        return null;
    }

    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties($this->properties)->id;
    }

}
