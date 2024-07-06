<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;
use DateTime;

final class Ticket
{
    use Serializable;

    public int $id;
    public string $ticket_code;
    public int $user_id;
    public ?string $comment;
    public ?int $discount_club_id;
    public int $price;
    public DateTime $booked_at;
    public DateTime $paid_at;
}
