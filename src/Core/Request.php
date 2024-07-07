<?php

namespace Skop\Core;

function validateOne(array $source, string $key, array $restrictions)
{
    if (!isset($source[$key]) || $source[$key] == null)
        throw new ErrorPageException(SKOP_ERROR_INPUT_MISSING, "Source missing key '$key' or value was null");

    $value = trim($source[$key]);

    // Validacija tipa
    switch ($restrictions['type'])
    {
    case 'string|alphabetical':
        if (!preg_match("/^[a-zA-ZабвгдђежзијклљмнњопрстћуфхцчџшАБВГДЂЕЖЗИЈКЛЉМНЊОПРСТЋУФХЦЧЏШćĆčČšŠđĐ]+$/", $value))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid alphabetical string");
        break;
    case 'string|email':
        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid email address");
        break;
    case 'int':
        if (!filter_var($value, FILTER_VALIDATE_INT))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid int");
        $value = intval($value);
        break;
    case 'float':
        if (!filter_var($value, FILTER_VALIDATE_FLOAT))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid float");
        $value = floatval($value);
        break;
    }

    // Validacija veličine
    $valueSize = null;
    switch ($restrictions['type'])
    {
    case 'string':
    case 'string|email':
        $valueSize = strlen($value);
        break;
    case 'int':
    case 'float':
        $valueSize = $value;
        break;
    }

    switch ($restrictions['type'])
    {
    case 'string':
    case 'int':
    case 'float':
        if (isset($restrictions['min']) && $valueSize < $restrictions['min'])
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' string was too short or number was too small");
        if (isset($restrictions['max']) && $valueSize > $restrictions['max'])
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' string was too long or number was too large");
        break;
    }

    return $value;
}

final class Request
{
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

    public function validatePostInput(array $route)
    {
        if (!isset($route['dataPost']))
            return;
        foreach ($route['dataPost'] as $key => $restrictions)
            validateOne($this->data, $key, $restrictions);
    }
}
