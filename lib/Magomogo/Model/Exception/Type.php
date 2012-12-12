<?php
namespace Magomogo\Model\Exception;

class Type extends \Exception
{
    public function __construct()
    {
        parent::__construct('Type not implemented');
    }
}
