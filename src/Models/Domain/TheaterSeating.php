<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class TheaterSeating extends DomainObject
{
    public int $theater_id;
    public int $seat_type_id;
    public bool $active;
    public int $row;
    public int $column;

    public static array $columnTraits = [
        'id'            => ['type' => 'int'         , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => BIGINT_U_MAX],
        'theater_id'    => ['type' => 'int|theater' , 'editable' => false, 'partial' => false, 'min' => 0, 'max' => TINYINT_U_MAX],
        'seat_type_id'  => ['type' => 'int|seattype', 'editable' => false, 'partial' => false, 'min' => 0, 'max' => TINYINT_U_MAX],
        'active'        => ['type' => 'bool'        , 'editable' => true , 'partial' => false],
        'row'           => ['type' => 'int'         , 'editable' => false, 'partial' => false, 'min' => 0, 'max' => 31],
        'column'        => ['type' => 'int'         , 'editable' => false, 'partial' => false, 'min' => 0, 'max' => 31]
    ];
}
