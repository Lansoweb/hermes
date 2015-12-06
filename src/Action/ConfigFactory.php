<?php
namespace Demeter\Action;

use Demeter\Storage\FileStorage;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigFactory implements AbstractFactoryInterface
{

    /**
     *
     * {@inheritDoc}
     *
     * @see \Zend\ServiceManager\AbstractFactoryInterface::canCreateServiceWithName()
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return in_array($requestedName, [
            GetAction::class,
            CreateAction::class,
            UpdateAction::class,
            DeleteAction::class,
            SetHeader::class,
        ]);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Zend\ServiceManager\AbstractFactoryInterface::createServiceWithName()
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $storage = new FileStorage();
        return new $requestedName($storage);
    }
}
