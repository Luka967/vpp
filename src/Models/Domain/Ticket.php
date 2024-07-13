<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;
use Skop\Models\RepertoireModel;
use Skop\Models\TicketModel;

final class Ticket extends DomainObject
{
    public string $ticket_code;
    public int $user_id;
    public int $repertoire_id;
    public ?string $comment = null;
    public ?int $discount_club_id = null;
    public int $price;
    public string $booked_at;
    public ?string $paid_at = null;

    public function repertoireEntry(): Repertoire
    {
        return RepertoireModel::withId($this->repertoire_id);
    }
    public function pickedSeats(): array
    {
        return TicketModel::seatsPickedOf($this);
    }
    // Potrebno za kolone u /me
    public function screeningStart(): string
    {
        return $this->repertoireEntry()->screening_start;
    }
    public function movieTitle(): string
    {
        return $this->repertoireEntry()->movie()->title;
    }
    public function pickedSeatCount()
    {
        return count($this->pickedSeats());
    }

    public static array $columnTraits = [
        'id'                => ['type' => 'int'                 , 'editable' => false, 'partial' => true , 'min' => 0 , 'max' => BIGINT_U_MAX],
        'ticket_code'       => ['type' => 'string|alphabetic'   , 'editable' => false, 'partial' => false, 'min' => 15, 'max' => 15],
        'user_id'           => ['type' => 'int'                 , 'editable' => false, 'partial' => false, 'min' => 1 , 'max' => INT_U_MAX],
        'repertoire_id'     => ['type' => 'int'                 , 'editable' => false, 'partial' => false, 'min' => 1 , 'max' => BIGINT_U_MAX],
        'comment'           => ['type' => 'string'              , 'editable' => true , 'partial' => null , 'min' => 0 , 'max' => 255],
        'discount_club_id'  => ['type' => 'int'                 , 'editable' => true , 'partial' => null , 'min' => 0 , 'max' => TINYINT_U_MAX],
        'price'             => ['type' => 'int'                 , 'editable' => true , 'partial' => false, 'min' => 0 , 'max' => 9999],
        'booked_at'         => ['type' => 'datetime'            , 'editable' => true , 'partial' => false],
        'paid_at'           => ['type' => 'datetime'            , 'editable' => true , 'partial' => false]
    ];
}
