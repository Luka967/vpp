<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\Repertoire;

class RepertoireModel extends Model
{
    protected static string $tableName = '`repertoire`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\Repertoire';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `repertoire`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function fromId(int $id): Repertoire|bool
    {
        $q = Db::instance()->prepare("SELECT * FROM `repertoire` WHERE `id` = :id");
        $q->bindParam(':id', $id, \PDO::PARAM_INT);
        if (!$q->execute())
            return false;
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public function insert(Repertoire $item): bool
    {
        $q = Db::instance()->prepare("INSERT INTO `repertoire` (`id`, `movie_id`, `theater_id`, `screening_start`) VALUES (?, ?, ?, ?)");
        return $q->execute($item->asArray());
    }

    public function delete(int $id): bool
    {
        $q = Db::instance()->prepare("DELETE FROM `repertoire` WHERE `id` = :id");
        $q->bindParam(':id', $id, \PDO::PARAM_INT);
        return $q->execute();
    }
}
