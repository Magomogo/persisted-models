<?php
namespace CreditCard;
use Model\PropertyBag;
use Model\DataType\Text;

/**
 * @property Text $pan
 * @property Text $validMonth
 * @property Text $validYear
 * @property Text $ccv
 * @property Text $cardholderName
 */
class Properties extends PropertyBag
{
    private static function properties()
    {
        return array(
            'pan' => new Text(),
            'validMonth' => new Text(),
            'validYear' => new Text(),
            'ccv' => new Text(),
            'cardholderName' => new Text(),
        );
    }

    public function __construct()
    {
        parent::__construct(self::properties());
    }
}
