<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\Genre;
use Skop\Models\Domain\Movie;

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
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function withName(string $name): ?Genre
    {
        $q = Db::instance()->prepare("SELECT * FROM `genres` WHERE `name` = :name");
        $q->bindValue(':name', $name, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public static function ofMovie(Movie $movie): array
    {
        $q = Db::instance()->prepare(
            "SELECT `movie_genres`.`genre_id` AS `id`, `genres`.`name` FROM `movie_genres`
                INNER JOIN `genres` ON `genres`.`id` = `movie_genres`.`genre_id`
            WHERE `movie_genres`.`movie_id` = :id");
        $q->bindValue(':id', $movie->id, \PDO::PARAM_INT);
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function setForMovie(Movie $movie, array $genres)
    {
        Db::instance()->beginTransaction();
        Db::instance()
            ->prepare("DELETE FROM `movie_genres` WHERE `movie_genres`.`movie_id` = ?")
            ->execute([$movie->id]);
        $insertRowsBind = substr(str_repeat('(?, ?),', count($genres)), 0, -1);
        $insertRows = [];
        foreach ($genres as $genre)
        {
            $insertRows[] = $movie->id;
            $insertRows[] = $genre->id;
        }
        Db::instance()
            ->prepare("INSERT INTO `movie_genres` (`movie_id`, `genre_id`) VALUES $insertRowsBind")
            ->execute($insertRows);
        Db::instance()->commit();
    }
}
