<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class TheaterSeatType extends DomainObject
{
    public string $name;
    public int $price_adult;
    public ?int $price_child;

    public static array $columnTraits = [
        'id'            => ['type' => 'int'                 , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => TINYINT_U_MAX],
        'name'          => ['type' => 'string|objectname'   , 'editable' => true , 'partial' => false, 'min' => 1, 'max' => 31],
        'price_adult'   => ['type' => 'int'                 , 'editable' => true , 'partial' => false, 'min' => 1, 'max' => 9999],
        'price_child'   => ['type' => 'int'                 , 'editable' => true , 'partial' => null , 'min' => 1, 'max' => 9999]
    ];
}
