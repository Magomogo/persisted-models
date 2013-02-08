<?php
namespace JobRecord;
use Magomogo\Model\ContainerReadyAbstract;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Magomogo\Model\PropertyBag;

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
     * @param $id
     * @param null $valuesToSet
     * @return \Magomogo\Model\PropertyBag
     */
    public static function propertiesSample($id = null, $valuesToSet = null)
    {
        return new Properties($id, $valuesToSet);
    }

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom($container, $id)
    {
        $properties = $container->loadProperties(self::propertiesSample($id));

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
