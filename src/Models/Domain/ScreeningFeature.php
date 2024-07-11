<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class ScreeningFeature extends DomainObject
{
    public string $description;

    public static array $columnTraits = [
        'id'            => ['type' => 'int'                 , 'editable' => false, 'partial' => true , 'min' => 0, 'max' => TINYINT_U_MAX],
        'description'   => ['type' => 'string|objectname'   , 'editable' => true , 'partial' => false, 'min' => 0, 'max' => 127],
    ];
}
