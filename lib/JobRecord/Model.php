<?php
namespace JobRecord;
use Model\ContainerReadyInterface;
use Model\PropertyContainer\ContainerInterface;
use Company;

class Model implements ContainerReadyInterface
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @var \Company\Model
     */
    private $previousCompany;

    /**
     * @var \Company\Model
     */
    private $currentCompany;

    /**
     * @param \Model\PropertyContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public static function loadFrom(ContainerInterface $container, $id)
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

    public function __construct(Company\Model $currentCompany, \Company\Model $previousCompany, Properties $properties)
    {
        $this->currentCompany = $currentCompany;
        $this->previousCompany = $previousCompany;
        $this->properties = $properties;
    }

    /**
     * @param \Model\PropertyContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties(
            $this->properties,
            array(
                'currentCompany' => $this->currentCompany->propertiesFrom($container),
                'previousCompany' => $this->previousCompany->propertiesFrom($container)
            )
        )->id;

    }

    /**
     * Confirms that properties has correct origin
     *
     * @param \Model\PropertyContainer\ContainerInterface $container
     * @return \Model\PropertyBag
     */
    public function propertiesFrom(ContainerInterface $container)
    {
        return $this->properties->assertOriginIs($container);
    }
}
