<?php
namespace Keymarker;
use Model\PropertyBag;

/**
 * @property string $id
 * @property \DateTime $created
 */
class Properties extends PropertyBag
{
    private static function props()
    {
        return array(
            'created' => new \DateTime
        );
    }

    public function __construct($id)
    {
        parent::__construct(self::props(), $id);
    }
}
