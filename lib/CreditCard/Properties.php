<?php
namespace CreditCard;
use Model\PropertyBag;
use Model\DataType\Text;

/**
 * @property string $system
 * @property string $pan
 * @property string $validMonth
 * @property string $validYear
 * @property string $ccv
 * @property string $cardholderName
 */
class Properties extends PropertyBag
{
    private static function properties()
    {
        return array(
            'system' => new Text(),
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
