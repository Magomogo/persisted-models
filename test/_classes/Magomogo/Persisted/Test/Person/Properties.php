<?php
namespace Magomogo\Persisted\Test\Person;

use Magomogo\Persisted\CollectionOwnerInterface;
use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\Test\CreditCard\Model as CreditCard;
use Magomogo\Persisted\Test\CreditCard\Properties as CreditCardProperties;
use Magomogo\Persisted\Test\Keymarker;

/**
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property \Magomogo\Persisted\Test\CreditCard\Model $creditCard
 */
class Properties extends PropertyBag implements CollectionOwnerInterface
{
    /**
     * @var Keymarker\Collection
     */
    public $tags;

    protected function properties()
    {
        return array(
            'title' => '',
            'firstName' => '',
            'lastName' => '',
            'phone' => '',
            'email' => '',
            'creditCard' => new CreditCard(new CreditCardProperties),
            'birthDay' => new \DateTime('1970-01-01T00:00:00+07:00')
        );
    }

    protected function init()
    {
        $this->tags = new Keymarker\Collection($this);
    }

    public function collections()
    {
        $collections = new \stdClass();
        $collections->tags = $this->tags;
        return $collections;
    }
}
