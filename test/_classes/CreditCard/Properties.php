<?php
namespace CreditCard;
use Magomogo\Model\PropertyBag;

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
            'system' => '',
            'pan' => '',
            'validMonth' => '',
            'validYear' => '',
            'ccv' => '',
            'cardholderName' => '',
        );
    }

    public function __construct($id = null)
    {
        parent::__construct(self::properties(), $id);
    }
}
