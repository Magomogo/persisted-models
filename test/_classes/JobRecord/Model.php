<?php
namespace JobRecord;
use Magomogo\Persisted\ContainerReadyAbstract;
use Magomogo\Persisted\PropertyContainer\ContainerInterface;
use Magomogo\Persisted\PropertyBag;

class Model extends ContainerReadyAbstract
{
    /**
     * @var \Company\Model
     */
    private $previousCompany;

    /**
     * @var \Company\Model
     */
    private $currentCompany;

    /**
     * @param \Magomogo\Persisted\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id)
    {
        $properties = $container->loadProperties(new Properties($id));

        return new self(
            $properties->foreign()->currentCompany,
            $properties->foreign()->previousCompany,
            $properties
        );
    }

    /**
     * @param \Company\Model $currentCompany
     * @param $previousCompany
     * @param Properties $properties
     */
    public function __construct($currentCompany, $previousCompany, $properties)
    {
        $this->currentCompany = $currentCompany;
        $this->previousCompany = $previousCompany;
        $this->properties = $properties;
    }
}
