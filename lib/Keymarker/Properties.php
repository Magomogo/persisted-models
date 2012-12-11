<?php
namespace Keymarker;
use Model\PropertyBag;

/**
 * @property string $title
 * @property \DateTime $created
 */
class Properties extends PropertyBag
{
    private static function props()
    {
        return array(
            'title' => '',
            'created' => new \DateTime
        );
    }

    public function __construct($id = null)
    {
        parent::__construct(self::props(), $id);
    }
}
