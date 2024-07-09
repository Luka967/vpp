<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class DiscountClub extends DomainObject
{
    public string $name;
    public int $discount;

    public static array $columnTraits = [
        'id'        => ['type' => 'int'   , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => TINYINT_U_MAX],
        'name'      => ['type' => 'string', 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 31],
        'discount'  => ['type' => 'int'   , 'editable' => true , 'partial' => false, 'min' => 1, 'max' => 99],
    ];
}
