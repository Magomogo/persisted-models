<?php
namespace Magomogo\Persisted\Exception;

class UnknownReference extends \Exception
{
    public function __construct($name)
    {
        parent::__construct('Unknown reference "' . $name . '"');
    }
}
