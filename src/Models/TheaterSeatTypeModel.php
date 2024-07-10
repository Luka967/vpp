<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\TheaterSeatType;

class TheaterSeatTypeModel extends Model
{
    protected static string $tableName = '`theater_seat_types`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\TheaterSeatType';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `theater_seat_types`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function withId(int $id): ?TheaterSeatType
    {
        $q = Db::instance()->prepare("SELECT * FROM `theater_seat_types` WHERE `id` = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function withDescription(string $name): ?TheaterSeatType
    {
        $q = Db::instance()->prepare("SELECT * FROM `theater_seat_types` WHERE `name` = :name");
        $q->bindValue(':name', $name, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
}
