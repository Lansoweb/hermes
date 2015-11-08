<?php
namespace Hermes\Storage;

use Hermes\Exception\ServiceNotFoundException;
use Hermes\Exception\VersionNotFoundException;

class FileStorage implements StorageInterface
{
    protected $baseDir = 'data/services';

    public function __construct($baseDir = null)
    {
        if (!empty($baseDir)) {
            $this->baseDir = $baseDir;
        }

        if (!$this->isValid($this->baseDir)) {
            throw new \InvalidArgumentException(sprintf('Invalid directory "%s".', $this->baseDir));
        }
    }

    private function isValid($dirOrFile)
    {
        if (!file_exists($dirOrFile)) {
            return false;
        }

        if (!is_writable($dirOrFile)) {
            return false;
        }

        return true;
    }

    private function getLatestFile($service)
    {
        return $this->baseDir . DIRECTORY_SEPARATOR . $service . DIRECTORY_SEPARATOR . 'latest.version';
    }

    private function getConfigFile($service, $version)
    {
        $serviceDir = $this->baseDir . DIRECTORY_SEPARATOR . $service;

        if (!file_exists($serviceDir)) {
            throw new ServiceNotFoundException(sprintf('Service "%s" not found', $service));
        }

        if (empty($version)) {
            $version = $this->getLatestVersion($service);
        }

        $filename = $serviceDir . DIRECTORY_SEPARATOR . 'config.v' . $version;

        if (!file_exists($filename)) {
            throw new VersionNotFoundException(sprintf('Version "%s" not found for service "%s"', $version, $service));
        }

        return $filename;
    }

    public function getLatestVersion($service)
    {
        $latest = $this->getLatestFile($service);
        return file_exists($latest) ? (int) file_get_contents($latest) : 0;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::get()
     */
    public function get($service, $version = '')
    {
        return file_get_contents($this->getConfigFile($service, $version));
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::set()
     */
    public function set($service, $config, $version = 0)
    {
        $version = (int) $version;

        $serviceDir = $this->baseDir . DIRECTORY_SEPARATOR . $service;

        if (!file_exists($serviceDir)) {
            mkdir($serviceDir);
        }
        $latest = $this->getLatestFile($service);
        if (!file_exists($latest)) {
            if ($version === 0) {
                $version = 1;
            }
        } elseif ($version === 0) {
            $version = $this->getLatestVersion($service);
        }
        file_put_contents($latest, $version);

        if (is_array($config)) {
            $config = json_encode($config);
        }

        return file_put_contents($serviceDir . DIRECTORY_SEPARATOR . 'config.v' . $version, $config);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::getValue()
     */
    public function getValue($service, $key, $version = 'v1')
    {
        // TODO: Auto-generated method stub
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::setValue()
     */
    public function setValue($service, $key, $value, $version = 'v1')
    {
        // TODO: Auto-generated method stub
    }
}