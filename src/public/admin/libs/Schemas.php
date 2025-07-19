<?php
namespace stcms;

require_once __DIR__.'/Schema.php';
require_once __DIR__.'/Config.php';

use LogicException;
use RuntimeException;

class Schemas
{
    protected array $schemas;
    public function __construct()
    {
        $this->load();
    }
    public function getAll(): array
    {
        return $this->schemas;
    }
    public function get(string $name): Schema
    {
        foreach($this->schemas as $schema) {
            if ($schema->name() === $name) return $schema;
        }
        throw new LogicException('no such name: ' . $name);
    }
    public function exists(string $name): bool
    {
        foreach($this->schemas as $schema) {
            if ($schema->name() === $name) return true;
        }
        return false;
    }

    protected function load()
    {
        $path = static::makePath();
        if (!file_exists($path)) {
            throw new RuntimeException('schemas.json is missing :' . $path);
        }

        $contents = file_get_contents($path);
        $rows = json_decode($contents, true);
        foreach ($rows as $name => $row) {
            $this->schemas[] = Schema::parse($name, $row);
        }
    }

    protected static function makePath(): string
    {
        return realpath(Config::getSchemasFilePath());
    }
}
