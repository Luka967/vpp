<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class Theater extends DomainObject
{
    public string $name;
    public bool $active;

    public static array $columnTraits = [
        'id'            => ['type' => 'int'                 , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => TINYINT_U_MAX],
        'name'          => ['type' => 'string|objectname'   , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 63],
        'active'        => ['type' => 'bool'                , 'editable' => true , 'partial' => false],
    ];
}
