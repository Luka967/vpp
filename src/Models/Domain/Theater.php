<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;

final class Theater
{
    use Serializable;

    public int $id;
    public int $location_id;
    public string $name;
}
