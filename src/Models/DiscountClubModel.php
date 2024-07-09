<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\DiscountClub;

class DiscountClubModel extends Model
{
    protected static string $tableName = '`discount_clubs`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\DiscountClub';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `discount_clubs`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function fromId(int $id): DiscountClub
    {
        $q = Db::instance()->prepare("SELECT * FROM `discount_clubs` WHERE `id` = :id");
        $q->bindParam(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public function delete(DiscountClub $item)
    {
        Db::instance()
            ->prepare("DELETE FROM `discount_clubs` WHERE `id` = ?")
            ->execute([$item->id]);
    }
}
