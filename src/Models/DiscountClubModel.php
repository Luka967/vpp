<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Models\Domain\DiscountClub;

class DiscountClubModel
{
    const CLASS_PATH = 'Skop\\Models\\Domain\\DiscountClub';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `discount_clubs`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function fromId(int $id): DiscountClub|bool
    {
        $q = Db::instance()->prepare("SELECT * FROM `discount_clubs` WHERE `id` = :id");
        $q->bindParam(':id', $id, \PDO::PARAM_INT);
        if (!$q->execute())
            return false;
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public function insert(DiscountClub $item): bool
    {
        $q = Db::instance()->prepare("INSERT INTO `discount_clubs` (`id`, `name`, `discount`) VALUES (:id, :name, :discount)");
        $q->bindParam(':id', $item->id, \PDO::PARAM_INT);
        $q->bindParam(':name', $item->name, \PDO::PARAM_STR);
        $q->bindParam(':discount', $item->discount, \PDO::PARAM_INT);
        return $q->execute();
    }

    public function delete(DiscountClub $item): bool
    {
        $q = Db::instance()->prepare("DELETE FROM `discount_clubs` WHERE `id` = :id");
        $q->bindParam(':id', $item->id, \PDO::PARAM_INT);
        return $q->execute();
    }
}
