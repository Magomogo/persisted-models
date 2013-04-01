<?php
namespace Magomogo\Persisted\Exception;

class NotFound extends \Exception
{
    public function __construct()
    {
        parent::__construct('Properties are not found');
    }
}
