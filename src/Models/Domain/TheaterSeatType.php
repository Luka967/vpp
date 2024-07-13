<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class TheaterSeatType extends DomainObject
{
    public string $name;
    public int $price_adult;
    public ?int $price_adult_weekend;
    public ?int $price_child;
    public ?int $price_child_weekend;
    public ?string $extra;

    public static array $columnTraits = [
        'id'                    => ['type' => 'int'                 , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => TINYINT_U_MAX],
        'name'                  => ['type' => 'string|objectname'   , 'editable' => true , 'partial' => false, 'min' => 1, 'max' => 31],
        'price_adult'           => ['type' => 'int'                 , 'editable' => true , 'partial' => false, 'min' => 1, 'max' => 9999],
        'price_adult_weekend'   => ['type' => 'int'                 , 'editable' => true , 'partial' => null , 'min' => 1, 'max' => 9999],
        'price_child'           => ['type' => 'int'                 , 'editable' => true , 'partial' => null , 'min' => 1, 'max' => 9999],
        'price_child_weekend'   => ['type' => 'int'                 , 'editable' => true , 'partial' => null , 'min' => 1, 'max' => 9999],
        'extra'                 => ['type' => 'string'              , 'editable' => true , 'partial' => null , 'min' => 0, 'max' => 255]
    ];
}
