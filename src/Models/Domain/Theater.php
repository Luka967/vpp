<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class Theater
{
    use DomainObject;

    public int $id;
    public int $location_id;
    public string $name;
}
