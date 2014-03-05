<?php
namespace Magomogo\Persisted\Test\Person;

use Magomogo\Persisted\AbstractProperties;
use Magomogo\Persisted\Test\CreditCard;
use Magomogo\Persisted\Test\Keymarker;

class Properties extends AbstractProperties
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $birthDay = '1970-01-01T00:00:00+07:00';

    /**
     * @var CreditCard\Properties
     */
    public $creditCard;

    protected function init()
    {
        $this->creditCard = new CreditCard\Properties;
//        $this->hasCollection(new Keymarker\Collection(), 'tags');
    }

    /**
     * @return CreditCard\Model
     */
    public function creditCard()
    {
        return $this->creditCard ? new CreditCard\Model($this->creditCard) : null;
    }

    /**
     * @return \DateTime
     */
    public function birthDay()
    {
        return $this->birthDay ? new \DateTime($this->birthDay) : null;
    }
}
