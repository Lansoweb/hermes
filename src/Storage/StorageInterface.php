<?php
namespace Hermes\Storage;

interface StorageInterface
{
    public function get($service, $version = '');

    public function set($service, $config, $version = '');

    public function getValue($service, $key, $version = '');

    public function setValue($service, $key, $value, $version = 'v');

    public function getLatestVersion($service);
}