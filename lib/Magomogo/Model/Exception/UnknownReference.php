<?php
namespace Magomogo\Model\Exception;

class UnknownReference extends \Exception
{
    public function __construct($name)
    {
        parent::__construct('Unknown reference "' . $name . '"');
    }
}
