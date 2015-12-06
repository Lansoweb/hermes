<?php
namespace Demeter\Storage;

class PdoStorage implements StorageInterface
{
    /**
     *
     * @var \PDO
     */
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::get()
     */
    public function get($key)
    {
        $sql = 'SELECT value FROM demeter where name = :key';
        $sth = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $sth->execute(array(':key' => $key));
        $value = $sth->fetch(\PDO::FETCH_ASSOC);
        return $value;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::set()
     */
    public function set($key, $value)
    {
        $data = $this->get($key);
        if (empty($data)) {
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

        $sql = 'UPDATE demeter set value = :value where key = :key';
        $sth = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $sth->execute([
            ':key' => $key,
            ':value' => json_encode($data),
        ]);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::has()
     */
    public function has($key)
    {
        $sql = 'SELECT value FROM demeter where name = :key';
        $sth = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $sth->execute(array(':key' => $key));
        return $sth->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::getIndex()
     */
    public function getIndex()
    {
        $value = $this->get('_demeter.idx');
        if (empty($value)) {
            $this->incrementIndex();
            $value = $this->get('_demeter.idx');
        }
        return $value;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::incrementIndex()
     */
    public function incrementIndex()
    {
        $value = $this->get('_demeter.idx');
        if (empty($value)) {
            $sql = 'INSERT INTO demeter set value = :value, key = :key';
            $sth = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $sth->execute([
                ':key' => '_demeter.idx',
                ':value' => 1,
            ]);
            return 1;
        }

        $sql = 'UPDATE demeter set value = value + 1 where key = :key';
        $sth = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $sth->execute([
            ':key' => '_demeter.idx',
        ]);

        return $this->get('_demeter.idx');
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Hermes\Storage\StorageInterface::delete()
     */
    public function delete($key)
    {
        $sql = 'DELETE FROM demeter where key = :key';
        $sth = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $sth->execute([
            ':key' => $key,
        ]);
    }
}