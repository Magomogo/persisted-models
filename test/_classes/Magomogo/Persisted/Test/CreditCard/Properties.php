<?php
namespace Magomogo\Persisted\Test\CreditCard;

use Magomogo\Persisted\AbstractProperties;

/**
 * @property string $system
 * @property string $pan
 * @property string $validMonth
 * @property string $validYear
 * @property string $ccv
 * @property string $cardholderName
 */
class Properties extends AbstractProperties
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
