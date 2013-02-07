<?php
namespace Keymarker;
use Magomogo\Model\ContainerReadyAbstract;

class Model extends ContainerReadyAbstract
{
    public function __construct(Properties $props)
    {
        $this->properties = $props;
    }

    public function __toString()
    {
        return $this->properties->id;
    }
}
