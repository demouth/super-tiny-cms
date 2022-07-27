<?php
namespace stcms;

use RuntimeException;

class Schema
{
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_URL = 'url';
    const TYPE_DATE = 'date';
    protected string $name;
    protected array $cols;
    public static function parse(string $name, array $array): Schema
    {
        $self = new static();
        $self->name = $name;
        foreach ($array as $name => $type) {
            switch ($type) {
                case static::TYPE_TEXT:
                    // fallthrough
                case static::TYPE_TEXTAREA:
                    // fallthrough
                case static::TYPE_URL:
                    // fallthrough
                case static::TYPE_DATE:
                    break;
                default:
                    throw new RuntimeException('no such type : '. $type);
                    break;
            }
        }
        $self->cols = $array;
        return $self;
    }
    public function name(): string
    {
        return $this->name;
    }
    public function getAll(): array
    {
        return $this->cols;
    }
}
