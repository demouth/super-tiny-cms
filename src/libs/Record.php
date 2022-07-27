<?php
namespace stcms;

use LogicException;

class Record implements \JsonSerializable
{
    protected array $data;
    protected float $createdAt;
    protected float $updatedAt;
    protected ?float $deletedAt;
    public static function create(): Record
    {
        $r = new static();
        $r->data = [];
        $r->createdAt = microtime(true);
        $r->updatedAt = $r->createdAt;
        $r->deletedAt = null;
        return $r;
    }
    public static function parse(array $array): Record
    {
        $r = new static();
        $r->data = $array['data'];
        $r->createdAt = $array['created_at'];
        $r->updatedAt = $array['updated_at'];
        $r->deletedAt = $array['deleted_at'];
        return $r;
    }
    public function get(string $key)
    {
        if (!$this->exists($key)) {
            throw new LogicException('no such key: ' . $key);
        }
        return $this->data[$key];
    }
    public function exists(string $key)
    {
        return isset($this->data[$key]);
    }
    public function keys(): array
    {
        return array_keys($this->data);
    }
    public function set(string $key, $in)
    {
        $this->data[$key] = $in;
        $this->updatedAt = microtime(true);
    }
    public function unset(string $key)
    {
        unset($this->data[$key]);
        $this->updatedAt = microtime(true);
    }
    public function delete()
    {
        $this->updatedAt = microtime(true);
        $this->deletedAt = $this->updatedAt;
    }
    public function createdAt(): float
    {
        return $this->createdAt;
    }
    public function updatedAt(): float
    {
        return $this->updatedAt;
    }
    public function deleted(): bool
    {
        return !is_null($this->deletedAt);
    }
    public function jsonSerialize() {
        return [
            'data' => $this->data,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'deleted_at' => $this->deletedAt,
        ];
    }
}
