<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class Ticket extends DomainObject
{
    public string $ticket_code;
    public int $user_id;
    public ?string $comment;
    public ?int $discount_club_id;
    public int $price;
    public string $booked_at;
    public string $paid_at;
}
