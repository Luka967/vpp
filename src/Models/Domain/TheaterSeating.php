<?php

namespace Skop\Models\Domain;

use Skop\Core\DomainObject;

final class TheaterSeating
{
    use DomainObject;

    public int $id;
    public int $theater_id;
    public int $seat_type_id;
    public bool $active;
    public int $row;
    public int $column;
}
