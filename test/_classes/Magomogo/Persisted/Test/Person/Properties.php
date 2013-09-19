<?php
namespace Magomogo\Persisted\Test\Person;

use Magomogo\Persisted\Collection;
use Magomogo\Persisted\PropertyBag;
use Magomogo\Persisted\Test\CreditCard;
use Magomogo\Persisted\Test\Keymarker;

/**
 * @property string $title
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $email
 * @property \Magomogo\Persisted\Test\CreditCard\Model $creditCard
 */
class Properties extends PropertyBag implements Collection\OwnerInterface
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
            'creditCard' => new CreditCard\Model(new CreditCard\Properties),
            'birthDay' => new \DateTime('1970-01-01T00:00:00+07:00')
        );
    }

    protected function init()
    {
        $this->tags = new Keymarker\Collection();
    }

    public function collections()
    {
        $collections = new \stdClass();
        $collections->tags = $this->tags;
        return $collections;
    }
}
