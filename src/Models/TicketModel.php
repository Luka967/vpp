<?php

namespace Skop\Models;

use Skop\Core\Db;
use Skop\Core\Model;
use Skop\Models\Domain\Ticket;

class TicketModel extends Model
{
    protected static string $tableName = '`tickets`';
    const CLASS_PATH = 'Skop\\Models\\Domain\\Ticket';
    const SEATING_CLASS_PATH = 'Skop\\Models\\Domain\\TheaterSeating';

    public static function all(): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `tickets`");
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function allOfUserUnpaid(int $userId): array
    {
        $q = Db::instance()->prepare("SELECT * FROM `tickets` WHERE `user_id` = ? AND `paid_at` IS NULL");
        $q->execute([$userId]);
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH);
    }

    public static function withId(int $id): ?Ticket
    {
        $q = Db::instance()->prepare("SELECT * FROM `tickets` WHERE `id` = :id");
        $q->bindValue(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function withCode(string $code): ?Ticket
    {
        $q = Db::instance()->prepare("SELECT * FROM `tickets` WHERE `ticket_code` = :code");
        $q->bindValue(':code', $code, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }
    public static function ofUserForRepertoireEntry(int $userId, int $repertoireEntryId): ?Ticket
    {
        $q = Db::instance()->prepare("SELECT * FROM `tickets` WHERE `user_id` = ? AND `repertoire_id` = ?");
        $q->execute([$userId, $repertoireEntryId]);
        if ($q->rowCount() == 0)
            return null;
        return $q->fetchAll(\PDO::FETCH_CLASS, static::CLASS_PATH)[0];
    }

    public static function generateTicketCode()
    {
        $characters = 'QWERTYUIOPASDFGHJKLZXCVBNM';
        $str = '';
        for ($i = 0; $i < 15; $i++)
            $str .= $characters[rand(0, strlen($characters) - 1)];
        return $str;
    }
    public static function insertOneWithPickedSeats(Ticket $partial, array $seatsPicked): Ticket
    {
        Db::instance()->beginTransaction();

        // Generiši tiket kod koji se ne pojavljuje u bazi
        while (true)
        {
            $partial->ticket_code = static::generateTicketCode();
            if (static::withCode($partial->ticket_code) == null)
                break;
        }

        static::insertOne($partial);
        $partial = static::withCode($partial->ticket_code);

        $insertRowsBind = substr(str_repeat('(?, ?),', count($seatsPicked)), 0, -1);
        $insertRows = [];
        foreach ($seatsPicked as $seat)
        {
            $insertRows[] = $partial->id;
            $insertRows[] = $seat->id;
        }
        Db::instance()
            ->prepare("INSERT INTO `ticket_seats` (`ticket_id`, `seat_id`) VALUES $insertRowsBind")
            ->execute($insertRows);

        Db::instance()->commit();
        return $partial;
    }

    public static function seatsPickedOf(Ticket $ticket)
    {
        $q = Db::instance()->prepare(
            "SELECT `theater_seating`.* FROM `ticket_seats`
                INNER JOIN `theater_seating` ON `theater_seating`.`id` = `ticket_seats`.`seat_id`
            WHERE `ticket_id` = :id");
        $q->bindValue(':id', $ticket->id, \PDO::PARAM_INT);
        $q->execute();
        return $q->fetchAll(\PDO::FETCH_CLASS, static::SEATING_CLASS_PATH);
    }
}
