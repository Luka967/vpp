<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Models\Domain\Genre;

class GenreModel
{
    const CLASS_PATH = 'Skop\\Models\\Domain\\Genre';

    public function insert(Genre $item): bool
    {
        $q = Db::instance()->prepare("INSERT INTO `genres` (`id`, `name`, `description`) VALUES (?, ?, ?)");
        return $q->execute($item->asArray());
    }

    public function delete(int $id): bool
    {
        return Db::instance()
            ->prepare("DELETE FROM `genres` WHERE `id` = ?")
            ->execute([$id]);
    }
}
