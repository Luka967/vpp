<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\Theater;
use Skop\Models\Domain\TheaterSeating;

class TheaterModel extends Model
{
    protected static string $tableName = '`theaters`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\Theater';
    const SEATING_CLASS_PATH = 'Skop\\Models\\Domain\\TheaterSeating';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `theaters`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function withId(int $id): ?Theater
    {
        $q = Db::instance()->prepare("SELECT * FROM `theaters` WHERE `id` = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function withName(string $name): ?Theater
    {
        $q = Db::instance()->prepare("SELECT * FROM `theaters` WHERE `name` = :name");
        $q->bindValue(':name', $name, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public static function seatingFor(Theater $theater): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `theater_seating` WHERE `theater_id` = :id AND `active` = 1");
        $q->bindValue(':id', $theater->id, \PDO::PARAM_INT);
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::SEATING_CLASS_PATH);
    }
    public static function seatingModelFor(Theater $theater): string
    {
        $seating = static::seatingFor($theater);

        $seatTypes = [];
        foreach (TheaterSeatTypeModel::all() as $type)
            $seatTypes[$type->id] = $type;

        $seating = TheaterModel::seatingFor($theater);
        $rowCount = 0;
        $colCount = 0;
        foreach ($seating as $seat)
        {
            $rowCount = max($rowCount, 1 + $seat->row);
            $colCount = max($colCount, 1 + $seat->column);
        }

        $seatObjects = [];
        for ($i = 0; $i < $rowCount * $colCount; $i++)
            $seatObjects[] = '';
        foreach ($seating as $seat)
            $seatObjects[$seat->row * $colCount + $seat->column] = $seatTypes[$seat->seat_type_id]->name;

        return "$rowCount;$colCount;" . join(';', $seatObjects);
    }

    private static function insertOneSeating(TheaterSeating $partial)
    {
        $bindings = $partial->generateInsertBindings();
        Db::instance()
            ->prepare("INSERT INTO `theater_seating` $bindings->query")
            ->execute($bindings->values);
    }
    public static function updateSeatingFor(Theater $theater, int $rowCount, int $colCount, array $seatTypes)
    {
        Db::instance()->beginTransaction();

        // Obriši sedišta koja nikad nisu bila korišćena
        Db::instance()
            ->prepare("DELETE FROM `theater_seating` WHERE `theater_id` = ? AND `active` = 1
                AND (SELECT COUNT(*) FROM `ticket_seats` WHERE `ticket_seats`.`seat_id` = `theater_seating`.`id`) = 0")
            ->execute([$theater->id]);

        // Sedišta koja su bila korišćena deaktiviraj tako da se više ne pojavljuju
        Db::instance()
            ->prepare("UPDATE `theater_seating` SET `active` = 0 WHERE `theater_id` = ? AND `active` = 1")
            ->execute([$theater->id]);

        for ($row = 0; $row < $rowCount; $row++)
            for ($col = 0; $col < $colCount; $col++)
            {
                $seatType = $seatTypes[$row * $colCount + $col];
                if ($seatType == null)
                    continue;
                $obj = new TheaterSeating();
                $obj->theater_id = $theater->id;
                $obj->seat_type_id = $seatType->id;
                $obj->row = $row;
                $obj->column = $col;
                $obj->active = 1;
                self::insertOneSeating($obj);
            }

        Db::instance()->commit();
    }
}
