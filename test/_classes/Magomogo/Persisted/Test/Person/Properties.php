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
     * @var CreditCard\Model
     */
    public $creditCard;

    /**
     * @var \DateTime
     */
    public $birthDay;

    protected function init()
    {
        $this->creditCard = new CreditCard\Model(new CreditCard\Properties);
        $this->birthDay = new \DateTime('1970-01-02T00:00:00+07:00');
        $this->hasCollection(new Keymarker\Collection(), 'tags');
    }
}
