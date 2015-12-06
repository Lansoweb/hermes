<?php
namespace Demeter\Storage;

final class FileStorage implements StorageInterface
{

    const INDEX_FILE = '_demeter.idx';

    private $baseDir = 'data/keys';

    public function __construct($baseDir = null)
    {
        if (! empty($baseDir)) {
            $this->baseDir = $baseDir;
        }

        if (! $this->isValid($this->baseDir)) {
            throw new \InvalidArgumentException(sprintf('Invalid directory "%s".', $this->baseDir));
        }
    }

    private function isValid($dirOrFile)
    {
        if (! file_exists($dirOrFile)) {
            return false;
        }

        if (! is_writable($dirOrFile)) {
            return false;
        }

        return true;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Demeter\Storage\StorageInterface::get()
     */
    public function get($key)
    {
        $filename = $this->baseDir . DIRECTORY_SEPARATOR . $key;

        return json_decode(file_get_contents($filename), true);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Demeter\Storage\StorageInterface::has()
     */
    public function has($key)
    {
        $filename = $this->baseDir . DIRECTORY_SEPARATOR . $key;

        return file_exists($filename);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Demeter\Storage\StorageInterface::set()
     */
    public function set($key, $value)
    {
        $filename = $this->baseDir . DIRECTORY_SEPARATOR . $key;

        if (file_exists($filename)) {
            $data = json_decode(file_get_contents($filename), true);
        } else {
            $data = [
                'key' => $key,
                'value' => null,
                'createdIndex' => 0,
                'modifiedIndex' => 0
            ];
        }
        $index = $this->incrementIndex();

        $data = array_merge($data, $value);

        if ($data['createdIndex'] === 0) {
            $data['createdIndex'] = $index;
        }
        $data['modifiedIndex'] = $index;

        file_put_contents($filename, json_encode($data));

        return $data;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Demeter\Storage\StorageInterface::getIndex()
     */
    public function getIndex()
    {
        $filename = $this->baseDir . DIRECTORY_SEPARATOR . self::INDEX_FILE;
        if (! file_exists($filename)) {
            $this->incrementIndex();
        }
        return file_get_contents($filename);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Demeter\Storage\StorageInterface::incrementIndex()
     */
    public function incrementIndex()
    {
        $filename = $this->baseDir . DIRECTORY_SEPARATOR . self::INDEX_FILE;
        $index = 1;
        if (!file_exists($filename)) {
            $index = 0;
        }
        $fp = fopen($filename, 'cb');
        flock($fp, LOCK_EX);
        if ($index !== 0) {
            $index = (int) trim(file_get_contents($filename));
        }
        ++ $index;
        fwrite($fp, $index);
        ftruncate($fp, strlen($index));
        flock($fp, LOCK_UN);
        fclose($fp);

        return $index;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Demeter\Storage\StorageInterface::delete()
     */
    public function delete($key)
    {
        $this->incrementIndex();

        $filename = $this->baseDir . DIRECTORY_SEPARATOR . $key;
        if (file_exists($filename)) {
            unlink($filename);
        }
        return true;
    }
}
