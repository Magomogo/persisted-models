<?php
namespace JobRecord;
use Model\ContainerReadyInterface;
use Model\DataContainer\ContainerInterface;
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
     * @param \Model\DataContainer\ContainerInterface $container
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
     * @param \Model\DataContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn(ContainerInterface $container)
    {
        return $container->saveProperties(
            $this->properties,
            array(
                'currentCompany' => $this->currentCompany->confirmOrigin($container),
                'previousCompany' => $this->previousCompany->confirmOrigin($container)
            )
        )->id;

    }

    /**
     * Confirms that properties has correct origin
     *
     * @param \Model\DataContainer\ContainerInterface $container
     * @return \Model\PropertyBag
     */
    public function confirmOrigin(ContainerInterface $container)
    {
        return $this->properties->confirmOrigin($container);
    }
}
