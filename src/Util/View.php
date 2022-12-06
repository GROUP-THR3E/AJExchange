<?php

namespace GroupThr3e\AJExchange\Util;

use InvalidArgumentException;

class View
{
    protected static string $path;
    protected string $name;
    protected array $data;

    public static function setPath(string $path): void
    {
        self::$path = $path;
    }

    protected function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->data = $data;
    }

    protected function renderInternal(): string
    {
        $fullPath = self::$path . DIRECTORY_SEPARATOR . $this->name . '.phtml';
        if (file_exists($fullPath)) {
            extract($this->data);
            ob_start();
            require_once $fullPath;
            $content = ob_get_clean();

            ob_start();
            require_once self::$path . DIRECTORY_SEPARATOR . '_layout.phtml';
            return ob_get_clean();
        }

        throw new InvalidArgumentException();
    }

    public static function render(string $name, array $data): string
    {
        $view = new View($name, $data);
        return $view->renderInternal();
    }
}