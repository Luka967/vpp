<?php

namespace Skop\Core;

use DateMalformedStringException;
use DateTime;

function validateOneArray(string $key, array &$sourceArray, array $restrictions)
{
    $valueRestrictions = [...$restrictions, 'type' => str_replace('array|', '', $restrictions['type'])];
    $valueCount = count($sourceArray);
    if (isset($restrictions['arrayMin']) && $valueCount < $restrictions['arrayMin'])
        throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' array has too few values");
    if (isset($restrictions['arrayMax']) && $valueCount > $restrictions['arrayMax'])
        throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' array has too many values");
    try
    {
        for ($i = 0; $i < $valueCount; $i++)
            validateOneValue($sourceArray, $i, $valueRestrictions);
    }
    catch (ErrorPageException $ex)
    {
        throw new ErrorPageException(
            $ex->errorPageCode,
            ($ex->extraDetails ?? 'No extra detail') . " while validating given '$key' array",
            $ex
        );
    }
}

function validateOneValue(array &$source, string $key, array $restrictions)
{
    if ($restrictions['type'] == 'ignore')
    {
        if (isset($restrictions['setToNull']))
            $source[$key] = null;
        else
            unset($source[$key]);
        return;
    }
    if (isset($restrictions['partial']) && $restrictions['partial'] === null && empty($source[$key]))
    {
        $source[$key] = null;
        return;
    }
    if (!isset($source[$key]) || $source[$key] == null)
        if (!isset($restrictions['optional']))
            throw new ErrorPageException(SKOP_ERROR_INPUT_MISSING, "Source missing key '$key' or value was null");

    if (str_starts_with($restrictions['type'], 'array|'))
    {
        // Ovaj input je niz
        validateOneArray($key, $source[$key], $restrictions);
        return;
    }

    $value = trim($source[$key]);

    // Validacija tipa
    switch ($restrictions['type'])
    {
    case 'string|alphabetical':
        if (!preg_match('/^[a-zA-ZабвгдђежзијклљмнњопрстћуфхцчџшАБВГДЂЕЖЗИЈКЛЉМНЊОПРСТЋУФХЦЧЏШćĆčČšŠđĐ]+$/', $value))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid alphabetical string");
        break;
    case 'string|objectname':
        if (!preg_match('/^[a-zA-ZабвгдђежзијклљмнњопрстћуфхцчџшАБВГДЂЕЖЗИЈКЛЉМНЊОПРСТЋУФХЦЧЏШćĆčČšŠđĐ0-9_\s]+$/', $value))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid object name string");
        break;
    case 'string|email':
        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid email address");
        break;
    case 'int':
    case 'int|permissions':
        if (!filter_var($value, FILTER_VALIDATE_INT))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid int");
        $value = intval($value);
        break;
    case 'float':
        if (!filter_var($value, FILTER_VALIDATE_FLOAT))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid float");
        $value = floatval($value);
    case 'date':
        try
        {
            $parsedDateTime = new DateTime($value);
            $value = $parsedDateTime->format('Y-m-d');
        }
        catch (DateMalformedStringException)
        {
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "Given '$key' is not a valid date");
        }
        break;
    }

    // Validacija veličine
    $valueSize = match ($restrictions['type'])
    {
        'string', 'string|email', 'string|objectname', 'string|alphabetical' => strlen($value),
        'int', 'int|permissions', 'float' => $value,
        default => -INF
    };

    switch ($restrictions['type'])
    {
    case 'string':
    case 'string|alphabetical':
    case 'string|objectname':
    case 'string|email':
    case 'int':
    case 'float':
        if (isset($restrictions['min']) && $valueSize < $restrictions['min'])
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' string was too short or number was too small");
        if (isset($restrictions['max']) && $valueSize > $restrictions['max'])
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' string was too long or number was too large");
        break;
    }

    $source[$key] = $value;
}

function validateOneFile(array &$source, string $key, array $restrictions)
{
    $fileMetadata = $source[$key];
    if ($fileMetadata['error'] == UPLOAD_ERR_NO_FILE)
    {
        if (!isset($restrictions['optional']))
            throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' file was not selected");
        else
        {
            unset($source[$key]);
            return;
        }
    }
    else if ($fileMetadata['error'] != UPLOAD_ERR_OK)
    {
        $error = $fileMetadata['error'];
        throw new ErrorPageException(0, "'$key' file could not get uploaded correctly ($error)");
    }
    if ($fileMetadata['size'] == 0)
        throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' file is empty");
    $filenameMatches = null;
    if (!preg_match("/^([a-zA-Z0-9_]{1,31})\.([a-zA-Z0-9_]{1,15})$/", $fileMetadata['name'], $filenameMatches))
        throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' file has bad name");
    if (isset($restrictions['mimeTypes']) && !in_array($fileMetadata['type'], $restrictions['mimeTypes'], true))
        throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' file is of bad type");
    if (isset($restrictions['fileExtensions']) && !in_array($filenameMatches[2], $restrictions['fileExtensions'], true))
        throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' file has bad extension");
}

final class Request
{
    public readonly string $method;
    public readonly string $path;
    public mixed $query = [];
    public mixed $data;
    public mixed $files;

    public function __construct()
    {
        $pathSplit = explode('?', $_SERVER['REQUEST_URI'], 2);
        $path = $pathSplit[0];
        if ($path[strlen($path) - 1] == '/')
            $path = substr($path, 0, -1);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $path;
        $this->data = $_POST;
        $this->files = $_FILES;
        if (count($pathSplit) > 1)
            parse_str($pathSplit[1], $this->query);
    }

    public function hasQueryKey(string $key): bool
    {
        return isset($this->query[$key]);
    }
    public function hasDataKey(string $key): bool
    {
        return isset($this->data[$key]);
    }
    public function hasFileUploaded(string $key): bool
    {
        return isset($this->files[$key]);
    }

    public function validateQueryInput(array $route)
    {
        if (!isset($route['dataQuery']))
            return;
        foreach ($route['dataQuery'] as $key => $restrictions)
            validateOneValue($this->query, $key, $restrictions);
        foreach ($this->query as $key => $_)
            if (!isset($route['dataQuery'][$key]))
                throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' query should not exist");
    }
    public function validatePostInput(array $route)
    {
        if (!isset($route['dataPost']))
            return;
        foreach ($route['dataPost'] as $key => $restrictions)
            validateOneValue($this->data, $key, $restrictions);
        foreach ($this->data as $key => $_)
            if (!isset($route['dataPost'][$key]))
                throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' should not exist");
    }
    public function validatePostFiles(array $route)
    {
        if (!isset($route['filesPost']))
            return;
        foreach ($route['filesPost'] as $key => $restrictions)
            validateOneFile($this->files, $key, $restrictions);
        foreach ($this->files as $key => $_)
            if (!isset($route['filesPost'][$key]))
                throw new ErrorPageException(SKOP_ERROR_INPUT_INVALID, "'$key' file should not exist");
    }
}
