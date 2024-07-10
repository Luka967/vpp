<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\Movie;

class MovieModel extends Model
{
    protected static string $tableName = '`movies`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\Movie';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `movies`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function withTitle(string $title): ?Movie
    {
        $q = Db::instance()->prepare("SELECT * FROM `movies` WHERE `title` = ?");
        $q->execute([$title]);
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public static function withId(int $id): ?Movie
    {
        $q = Db::instance()->prepare("SELECT * FROM `movies` WHERE `id` = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
}
