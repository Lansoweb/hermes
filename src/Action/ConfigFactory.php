<?php
namespace Demeter\Action;

use Demeter\Storage\FileStorage;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Demeter\Storage\PdoStorage;

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
        $config = $serviceLocator->get('config');

        if (!isset($config['demeter']) || !isset($config['demeter']['storage'])) {
            throw new \InvalidArgumentException("Missing configuration options");
        }

        $demeterConfig = $config['demeter'];
        if ($demeterConfig['storage']['type'] == 'pdo') {
            $dsn = $demeterConfig['storage']['dsn'];
            $username = $demeterConfig['storage']['username'];
            $password = $demeterConfig['storage']['password'];
            $db = new \PDO($dsn,$username,$password);
            $storage = new PdoStorage($db);
        } else {
            $dir = $demeterConfig['storage']['dir'];
            $storage = new FileStorage($dir);
        }

        return new $requestedName($storage);
    }
}
