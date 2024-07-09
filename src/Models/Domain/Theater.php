<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class Theater extends DomainObject
{
    public string $name;

    public static array $columnTraits = [
        'id'            => ['type' => 'int'   , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => TINYINT_U_MAX],
        'name'          => ['type' => 'string', 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 63],
    ];
}
