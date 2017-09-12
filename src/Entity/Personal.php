<?php
namespace FiremonPHP\Auth\Entity;


class Personal
{
    public function __get($name)
    {
        return $this->{$name} ?? null;
    }

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
}