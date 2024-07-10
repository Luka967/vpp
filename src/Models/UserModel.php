<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\User;

class UserModel extends Model
{
    protected static string $tableName = '`users`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\User';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `users`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function withId(int $id): ?User
    {
        $q = Db::instance()->prepare("SELECT * FROM `users` WHERE `id` = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function withEmail(string $email): ?User
    {
        $q = Db::instance()->prepare("SELECT * FROM `users` WHERE `email` = :val");
        $q->bindValue(':val', $email, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
}
