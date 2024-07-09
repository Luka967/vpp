<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class Genre extends DomainObject
{
    public string $name;

    public static array $columnTraits = [
        'id'            => ['type' => 'int'   , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => SMALLINT_U_MAX],
        'name'          => ['type' => 'string', 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 31],
    ];
}
