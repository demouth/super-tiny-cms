<?php
namespace stcms;

require_once __DIR__.'/Record.php';

use LogicException;

class RecordSet implements \JsonSerializable
{
    protected array $records = [];

    public function __construct()
    {
    }
    public static function parse(array $array): RecordSet
    {
        $rs = new static();
        $rs->records = [];
        foreach ($array as $key => $row) {
            $r = Record::parse($row);
            $rs->records[$key] = $r;
        }
        return $rs;
    }
    public function add(Record $record)
    {
        $newId = $this->nextId();
        $this->records[$newId] = $record;
    }
    public function getAll(): array
    {
        return $this->records;
    }
    public function get(int $id): Record
    {
        if ($this->exists($id)) {
            return $this->records[$id];
        }
        throw new LogicException('no such id: ' . $id);
    }
    public function delete(int $id): bool
    {
        if (!$this->exists($id)) return false;
        $this->records[$id]->delete();
        return true;
    }
    public function exists(int $id): bool
    {
        return isset($this->records[$id]);
    }
    protected function nextId(): int
    {
        $maxId = 0;
        foreach($this->records as $id => $_) {
            if ($maxId < $id) $maxId = $id;
        }
        return $maxId + 1;
    }
    public function jsonSerialize()
    {
        return $this->records;
    }
}
