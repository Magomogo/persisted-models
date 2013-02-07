<?php
namespace JobRecord;
use Magomogo\Model\ContainerReadyAbstract;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Company\Model as Company;
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
        return new PropertyBag(
            'job_record',
            $id,
            array(),
            array(
                'currentCompany' => Company::propertiesSample(),
                'previousCompany' => Company::propertiesSample()
            ),
            $valuesToSet
        );
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
            $properties->reference('currentCompany'),
            $properties->reference('previousCompany'),
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
