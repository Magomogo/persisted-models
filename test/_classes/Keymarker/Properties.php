<?php
namespace Keymarker;
use Magomogo\Model\PropertyBag;

/**
 * @property string $id
 * @property \DateTime $created
 */
class Properties extends PropertyBag
{
    protected static function properties()
    {
        return array(
            'created' => new \DateTime
        );
    }
}
