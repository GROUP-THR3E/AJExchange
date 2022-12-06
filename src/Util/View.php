<?php

namespace GroupThr3e\AJExchange\Util;

class View
{
    protected static string $path;
    protected string $name;
    protected array $data;

    public static function setPath(string $path)
    {
        self::$path = $path;
    }

    protected function __construct(array $data)
    {
        $this->data = $data;
    }

    protected function renderInternal()
    {
        $fullPath = self::$path . DIRECTORY_SEPARATOR . $this->name;
        if (file_exists($fullPath)) {
            require_once $fullPath;
        }
    }
}