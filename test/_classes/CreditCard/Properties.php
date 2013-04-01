<?php
namespace CreditCard;
use Magomogo\Persisted\PropertyBag;

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
    protected function properties()
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
}
