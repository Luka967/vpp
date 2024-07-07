<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Models\Domain\Genre;

class GenreModel
{
    const CLASS_PATH = 'Skop\\Models\\Domain\\Genre';

    public function insert(Genre $partial)
    {
        Db::instance()
            ->prepare("INSERT INTO `genres` (`name`, `description`) VALUES (?, ?)")
            ->execute([$partial->name, $partial->description]);
    }

    public function delete(int $id)
    {
        Db::instance()
            ->prepare("DELETE FROM `genres` WHERE `id` = ?")
            ->execute([$id]);
    }
}
