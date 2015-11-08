<?php
namespace Hermes\Action;

use Hermes\Storage\FileStorage;
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
        return $requestedName === 'Hermes\Action\GetAction' || $requestedName === 'Hermes\Action\PostAction';
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
