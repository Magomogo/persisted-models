<?php
namespace JobRecord;
use Magomogo\Model\PropertyBag;

/**
 * @property string $id
 */
class Properties extends PropertyBag
{
    private static function properties()
    {
        return array();
    }

    public function __construct($id = null)
    {
        parent::__construct(self::properties(), $id);
    }
}
