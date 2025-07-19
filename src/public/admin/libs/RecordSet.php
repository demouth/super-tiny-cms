<?php
namespace stcms;

require_once __DIR__.'/Record.php';

use LogicException;

class RecordSet implements \JsonSerializable
{
    protected $records = array();

    public function __construct()
    {
    }
    public static function parse($array)
    {
        $rs = new static();
        $rs->records = array();
        foreach ($array as $key => $row) {
            $r = Record::parse($row);
            $rs->records[$key] = $r;
        }
        return $rs;
    }
    public function add($record)
    {
        $newId = $this->nextId();
        $this->records[$newId] = $record;
    }
    public function getAll()
    {
        return $this->records;
    }
    public function get($id)
    {
        if ($this->exists($id)) {
            return $this->records[$id];
        }
        throw new LogicException('no such id: ' . $id);
    }
    public function delete($id)
    {
        if (!$this->exists($id)) return false;
        $this->records[$id]->delete();
        return true;
    }
    public function exists($id)
    {
        return isset($this->records[$id]);
    }
    protected function nextId()
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
