<?php

namespace App\RemoteModel;

abstract class Model
{
    protected string $_name = '';

    protected array $fields = [];

    public function __set(string $name, $value)
    {
        $this->fields[$name] = $value;
    }

    public function __get(string $name)
    {
        return $this->fields[$name] ?? null;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function toArray()
    {
        return $this->fields;
    }
}
