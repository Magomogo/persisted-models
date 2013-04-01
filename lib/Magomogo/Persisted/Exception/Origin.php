<?php
namespace Magomogo\Persisted\Exception;

class Origin extends \Exception
{
    public function __construct()
    {
        parent::__construct('Properties origin mismatch');
    }
}
