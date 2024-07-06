<?php

namespace Skop\Core;

final class Request {
    public readonly string $method;
    public readonly string $path;
    public readonly mixed $data;

    public function __construct()
    {
        $path = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
        if ($path[strlen($path) - 1] === '/')
            $path = substr($path, 0, -1);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $path;
        $this->data = $_POST;
    }
}
