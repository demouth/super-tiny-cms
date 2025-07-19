<?php
namespace stcms;

use LogicException;

class Record implements \JsonSerializable
{
    protected $data;
    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;
    public static function create()
    {
        $r = new static();
        $r->data = array();
        $r->createdAt = microtime(true);
        $r->updatedAt = $r->createdAt;
        $r->deletedAt = null;
        return $r;
    }
    public static function parse($array)
    {
        $r = new static();
        $r->data = $array['data'];
        $r->createdAt = $array['created_at'];
        $r->updatedAt = $array['updated_at'];
        $r->deletedAt = $array['deleted_at'];
        return $r;
    }
    public function get($key)
    {
        if (!$this->exists($key)) {
            throw new LogicException('no such key: ' . $key);
        }
        return $this->data[$key];
    }
    public function exists($key)
    {
        return isset($this->data[$key]);
    }
    public function keys()
    {
        return array_keys($this->data);
    }
    public function set($key, $in)
    {
        $this->data[$key] = $in;
        $this->updatedAt = microtime(true);
    }
    public function remove($key)
    {
        unset($this->data[$key]);
        $this->updatedAt = microtime(true);
    }
    public function delete()
    {
        $this->updatedAt = microtime(true);
        $this->deletedAt = $this->updatedAt;
    }
    public function createdAt()
    {
        return $this->createdAt;
    }
    public function updatedAt()
    {
        return $this->updatedAt;
    }
    public function deleted()
    {
        return !is_null($this->deletedAt);
    }
    public function jsonSerialize() {
        return array(
            'data' => $this->data,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'deleted_at' => $this->deletedAt,
        );
    }
}
