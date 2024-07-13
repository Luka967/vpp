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
    public ?string $paid_at;

    public static array $columnTraits = [
        'id'            => ['type' => 'int'                 , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => TINYINT_U_MAX],
        'name'          => ['type' => 'string|objectname'   , 'editable' => true , 'partial' => false, 'min' => 1, 'max' => 31],
        'price_adult'   => ['type' => 'int'                 , 'editable' => true , 'partial' => false, 'min' => 1, 'max' => 9999],
        'price_child'   => ['type' => 'int'                 , 'editable' => true , 'partial' => null , 'min' => 1, 'max' => 9999],
        'extra'         => ['type' => 'string'              , 'editable' => true , 'partial' => null , 'min' => 0, 'max' => 255]
    ];
}
