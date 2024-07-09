<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\Genre;

class GenreModel extends Model
{
    protected static string $tableName = '`genres`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\Genre';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `genres`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function withId(int $id): ?Genre
    {
        $q = Db::instance()->prepare("SELECT * FROM `genres` WHERE `id` = :id");
        $q->bindParam(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
}
