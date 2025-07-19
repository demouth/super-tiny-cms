<?php
namespace stcms;

require_once __DIR__.'/RecordSet.php';
require_once __DIR__.'/Record.php';
require_once __DIR__.'/Config.php';

use LogicException;
use RuntimeException;

class Database
{
    protected RecordSet $data;
    protected float $createdAt;
    protected float $updatedAt;
    protected string $schema = '';

    public function __construct(string $schema)
    {
        $this->setSchema($schema);
    }

    public function setSchema(string $schema): bool
    {
        if (!preg_match('/^[a-z_-]{1,10}$/', $schema)) {
            throw new LogicException('invalid schema');
        }
        $this->schema = $schema;
        return $this->load();
    }

    public function get(): RecordSet
    {
        return $this->data;
    }

    public function set(RecordSet $data)
    {
        $this->data = $data;
        return $this->write();
    }

    protected function write()
    {
        // backup
        $path = static::makePath($this->schema);
        if (file_exists($path)) {
            $backupPath = $path . '.' . str_replace('.','_',microtime(true));
            $r = rename($path, $backupPath);
            if (!$r) {
                throw new RuntimeException('failed to backup');
            }
        }

        // save
        $json = json_encode(
            [
                'data' => $this->data,
                'created_at' => $this->createdAt,
                'updated_at' => microtime(true),
            ],
            JSON_PRETTY_PRINT
        );
        $r = file_put_contents($path, $json);
        if ($r === false) {
            throw new RuntimeException('failed to write DB file');
        }
    }

    protected function load(): bool
    {
        $path = static::makePath($this->schema);
        if (!file_exists($path)) {
            $this->data = new RecordSet();
            $this->createdAt = microtime(true);
            $this->updatedAt = $this->createdAt;
            return false;
        }

        $contents = file_get_contents($path);
        $object = json_decode($contents, true);
        if (!$object) {
            $this->data = new RecordSet();
            $this->createdAt = microtime(true);
            $this->updatedAt = $this->createdAt;
            return false;
        }
        if (!$object['data']) {
            $this->data = new RecordSet();
            $this->createdAt = microtime(true);
            $this->updatedAt = $this->createdAt;
            return false;
        }

        $this->data = RecordSet::parse($object['data']);
        $this->createdAt = $object['created_at'];
        $this->updatedAt = $object['updated_at'];
        return true;
    }

    protected static function makePath(string $schema): string
    {
        $dataDir = Config::getDataDirPath();
        $path = $dataDir . '/' . $schema . '.json';
        return $path;
    }
}
