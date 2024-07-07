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

    public static function getOneById(int $id): ?User
    {
        $q = Db::instance()->prepare("SELECT * FROM `users` WHERE `id` = :id");
        $q->bindParam(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function getOneByEmail(string $email): ?User
    {
        $q = Db::instance()->prepare("SELECT * FROM `users` WHERE `email` = :val");
        $q->bindParam(':val', $email, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public static function createOne(User $partialUser)
    {
        $q = Db::instance()->prepare("INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`) VALUES (:fn, :ln, :e, :p)");
        $q->bindParam(':fn', $partialUser->first_name);
        $q->bindParam(':ln', $partialUser->last_name);
        $q->bindParam(':e', $partialUser->email);
        $q->bindParam(':p', $partialUser->password);
        $q->execute();
    }
}
