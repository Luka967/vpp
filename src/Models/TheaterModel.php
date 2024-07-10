<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\Theater;
use Skop\Models\Domain\TheaterSeatType;

class TheaterModel extends Model
{
    protected static string $tableName = '`theaters`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\Theater';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `theaters`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function withId(int $id): ?Theater
    {
        $q = Db::instance()->prepare("SELECT * FROM `theaters` WHERE `id` = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function withDescription(string $name): ?Theater
    {
        $q = Db::instance()->prepare("SELECT * FROM `theaters` WHERE `name` = :name");
        $q->bindValue(':name', $name, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
}
