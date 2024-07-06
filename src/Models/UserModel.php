<?php

namespace Skop\Models;

use Exception;
use Skop\Core\Db;
use Skop\Models\Domain\User;

class UserModel
{
    const CLASS_PATH = 'Skop\\Models\\Domain\\User';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `users`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function getOneById(int $id): User|bool
    {
        $q = Db::instance()->prepare("SELECT * FROM `users` WHERE `id` = :id");
        $q->bindParam(':id', $id, \PDO::PARAM_INT);
        if (!$q->execute())
            return false;
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function getOneByEmail(string $email): User|bool
    {
        $q = Db::instance()->prepare("SELECT * FROM `users` WHERE `email` = :val");
        $q->bindParam(':val', $email, \PDO::PARAM_STR);
        if (!$q->execute())
            return false;
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
}
