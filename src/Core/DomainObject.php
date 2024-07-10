<?php

namespace Skop\Core;

abstract class DomainObject
{
    public ?int $id = null;
    public static array $columnTraits = [];

    public function asArray(): array
    {
        $ret = [];
        foreach ($this as $key => $value)
            $ret[$key] = $value;
        return $ret;
    }

    public function generateInsertBindings()
    {
        $columnList = [];
        $bindList = [];
        $valueList = [];
        foreach (static::$columnTraits as $key => $traits)
        {
            if (static::$columnTraits[$key]['partial'] === true)
                continue;
            $columnList[] = "`$key`";
            $bindList[] = '?';
            $valueList[] = $this->$key;
        }
        return new DomainObjectBinding(
            query: '(' . join(', ', $columnList) . ') VALUES (' . join(', ', $bindList) . ')',
            values: $valueList
        );
    }

    public function generateUpdateBindings(?array $selectColumns = null)
    {
        $bindList = [];
        $valueList = [];
        foreach (static::$columnTraits as $key => $traits)
        {
            if (!static::$columnTraits[$key]['editable'])
                continue;
            if ($selectColumns != null && !in_array($key, $selectColumns, true))
                continue;
            $bindList[] = "`$key` = ?";
            $valueList[] = $this->$key;
        }
        return new DomainObjectBinding(
            query: join(', ', $bindList),
            values: $valueList
        );
    }
}
