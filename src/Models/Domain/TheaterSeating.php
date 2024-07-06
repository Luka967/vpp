<?php

namespace Skop\Models\Domain;

use Skop\Core\Serializable;

final class TheaterSeating
{
    use Serializable;

    public int $id;
    public int $theater_id;
    public int $seat_type_id;
    public bool $active;
    public int $row;
    public int $column;
}
