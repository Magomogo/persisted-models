<?php
namespace Magomogo\Persisted\Test\CreditCard;

use Magomogo\Persisted\AbstractProperties;

class Properties extends AbstractProperties
{
    /**
     * @var string
     */
    public $system;

    /**
     * @var string
     */
    public $pan;

    /**
     * @var string
     */
    public $validMonth;

    /**
     * @var string
     */
    public $validYear;

    /**
     * @var string
     */
    public $ccv;

    /**
     * @var string
     */
    public $cardholderName;
}
