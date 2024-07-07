<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Models\Domain\Movie;

class MovieModel
{
    const CLASS_PATH = 'Skop\\Models\\Domain\\Movie';

    public function insert(Movie $item): bool
    {
        $q = Db::instance()->prepare("INSERT INTO `movies` (
            `id`, `title`, `original_title`, `producer_studio`, `release_date`, `synopsis`,
            `runtime`, `director`, `significant_cast_1`, `significant_cast_2`, `significant_cast_3`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $q->execute($item->asArray());
    }

    public function delete(int $id): bool
    {
        $q = Db::instance()->prepare("DELETE FROM `movies` WHERE `id` = :id");
        $q->bindParam(':id', $id, \PDO::PARAM_INT);
        return $q->execute();
    }
}
