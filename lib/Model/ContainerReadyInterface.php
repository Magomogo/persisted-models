<?php
namespace Model;
use Model\DataContainer\ContainerInterface;

interface ContainerReadyInterface
{
    /**
     * @param DataContainer\ContainerInterface $container
     * @param string $id
     * @return self
     */
    public function loadFrom(ContainerInterface $container, $id);

    /**
     * @param DataContainer\ContainerInterface $container
     * @return string unique identifier
     */
    public function putIn(ContainerInterface $container);
}
