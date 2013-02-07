<?php
namespace JobRecord;
use Magomogo\Model\ContainerReadyAbstract;
use Magomogo\Model\PropertyContainer\ContainerInterface;
use Company;

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
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public function newFrom($container, $id)
    {
        $properties = new Properties(
            $id,
            array(
                'currentCompany' => new Company\Properties(),
                'previousCompany' => new Company\Properties()
            )
        );
        $container->loadProperties($properties);

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
