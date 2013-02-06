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
    public static function loadFrom($container, $id)
    {
        $properties = new Properties($id);
        $references = array(
            'currentCompany' => new Company\Properties(),
            'previousCompany' => new Company\Properties()
        );
        $container->loadProperties($properties, $references);

        return new self(
            new Company\Model($references['currentCompany']),
            new Company\Model($references['previousCompany']),
            $properties
        );
    }

    /**
     * @param \Company\Model $currentCompany
     * @param \Company\Model $previousCompany
     * @param Properties $properties
     */
    public function __construct($currentCompany, $previousCompany, $properties)
    {
        $this->currentCompany = $currentCompany;
        $this->previousCompany = $previousCompany;
        $this->properties = $properties;
    }

    /**
     * @param \Magomogo\Model\PropertyContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn($container)
    {
        return $container->saveProperties(
            $this->properties,
            array(
                'currentCompany' => $this->currentCompany->propertiesFrom($container),
                'previousCompany' => $this->previousCompany->propertiesFrom($container)
            )
        )->id;

    }
}
