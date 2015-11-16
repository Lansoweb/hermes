<?php
namespace Hermes\Storage;

interface StorageInterface
{
    public function get($key);

    public function set($key, $value);

    public function has($key);

    public function getIndex();

    public function incrementIndex();

    public function delete($key);
}
