<?php
namespace Company;
use Model\PropertyBag;

/**
 * @property string $name
 */
class Properties extends PropertyBag
{
    private static function properties()
    {
        return array(
            'name' => '',
        );
    }

    public function __construct($id = null)
    {
        parent::__construct(self::properties(), $id);
    }
}
