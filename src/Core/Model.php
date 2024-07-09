<?php

namespace Skop\Core;
use Skop\Core\DomainObject;

abstract class Model
{
    protected static string $tableName = '???';

    public static function insertOne(DomainObject $partial)
    {
        $tableName = static::$tableName;
        $bindings = $partial->generateInsertBindings();
        print_r($bindings);
        Db::instance()
            ->prepare("INSERT INTO $tableName $bindings->query")
            ->execute($bindings->values);
    }

    public static function updateOne(DomainObject $object, ?array $selectColumns = null)
    {
        $tableName = static::$tableName;
        $bindings = $object->generateUpdateBindings($selectColumns);
        Db::instance()
            ->prepare("UPDATE $tableName SET $bindings->query WHERE `id` = ?")
            ->execute(array_merge([...$bindings->values, $object->id]));
    }

    public static function deleteOne(int $id)
    {
        $tableName = static::$tableName;
        Db::instance()
            ->prepare("DELETE FROM $tableName WHERE `id` = ?")
            ->execute([$id]);
    }
}
