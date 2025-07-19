<?php
namespace stcms;

use RuntimeException;

class Schema
{
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_URL = 'url';
    const TYPE_DATE = 'date';
    const TYPE_IMAGE = 'image';
    protected $name;
    protected $cols;
    public static function parse($name, $array)
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
                    // fallthrough
                case static::TYPE_IMAGE:
                    break;
                default:
                    throw new RuntimeException('no such type : '. $type);
                    break;
            }
        }
        $self->cols = $array;
        return $self;
    }
    public function name()
    {
        return $this->name;
    }
    public function getAll()
    {
        return $this->cols;
    }
}
